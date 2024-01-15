<?php

namespace Modules\User\Http\Controllers\Api;

use App\Services\SendMailService;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Config\ConfigEntity;
use Modules\Kizuner\Exceptions\PermissionDeniedException;
use Modules\KizunerApi\Transformers\TutorialSettingTransform;
use Modules\User\Exceptions\WrongPinException;
use Modules\User\Http\Requests\Api\User\EmailPinVerifyRequest;
use Modules\User\Http\Requests\Api\User\PhoneConfirmRequest;
use Modules\User\Http\Requests\Api\User\SignInByEmailRequest;
use Modules\User\Http\Requests\Api\User\SignInBySocialRequest;
use Modules\User\Http\Requests\Api\User\SignUpRequest;
use Modules\User\Services\User\AuthManager;
use Modules\User\Services\UserManager;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;

class AuthController
{
    /**
     * @param SignInByEmailRequest $request
     * @param AuthManager $authManager
     * @return JsonResponse
     */
    public function signIn(SignInByEmailRequest $request, AuthManager $authManager)
    {
        if ($request->validated()) {
            return $authManager->signInByEmail($request);
        }
    }

    /**
     * @param SignInBySocialRequest $request
     * @param AuthManager $authManager
     * @return JsonResponse
     * @throws \Modules\User\Exceptions\MissingInfoException
     */
    public function socialSignIn(SignInBySocialRequest $request, AuthManager $authManager)
    {
        if ($request->validated()) {
            try {
                return $authManager->signInBySocial($request->all());
            } catch (\Exception $exception) {
                return new JsonResponse([
                    'errors' => [
                        'message' => $exception->getMessage(),
                        'code'    => $exception->getCode()
                    ]
                ], Response::HTTP_BAD_REQUEST);
            }
        }
    }

    public function authCode(Request $request, AuthManager $authManager)
    {
      // $token = base64_encode(env('TWITTER_CLIENT_ID') . ":" . env('TWITTER_CLIENT_SECRET'));
      // $authHeader = 'Basic ' . $token;

      $response = Http::post('https://api.twitter.com/2/oauth2/token', [
        'code' => $request->code,
        'grant_type' => 'authorization_code',
        'redirect_uri' => config('app.url') . '/api/redirect_code',
        'code_verifier' => $request->state,
        'client_id' => config('services.twitter.client_id'),
      ], [
          'headers' => [
              'Content-Type' => 'application/x-www-form-urlencoded'
          ],
      ]);

      if ($response->successful()) {
          $data = $response->json();

          $appScheme = 'com.kizuner.auth://';
          $deepLink = $appScheme . 'token=' . $data['access_token'];
          return redirect()->away($deepLink);
      } else {
          $errorMessage = $response->body();
          Log::debug($errorMessage);
      }
    }

    /**
     * @param AuthManager $authManager
     * @return JsonResponse
     */
    public function logout(AuthManager $authManager)
    {
        return $authManager->logout();
    }

    /**
     * @param UserManager $userManager
     * @param string|null $id
     * @return JsonResponse
     */
    public function getUser(UserManager $userManager, string $id = null)
    {
        return new JsonResponse(
            $userManager->getUser($id),
            Response::HTTP_OK
        );
    }

    /**
     * @param SignUpRequest $request
     * @param AuthManager $authManager
     * @return JsonResponse
     */
    public function signUp(SignUpRequest $request, AuthManager $authManager)
    {
        if ($request->validated()) {
            try {
                return new JsonResponse(
                    $authManager->createUser($request),
                    Response::HTTP_CREATED
                );
            } catch (\Exception $exception) {
                return response()->json([
                    'error' => [
                        'message' => 'Duplicate Email/Phone'
                    ]
                ]);
            }
        }
    }

    public function verifyEmail(AuthManager $authManager)
    {
        return new JsonResponse(
            $authManager->verifyEmail(),
            Response::HTTP_CREATED
        );
    }

    public function emailConfirmation(AuthManager $authManager, EmailPinVerifyRequest $request)
    {
        if ($request->validated()) {
            try {
                return new JsonResponse(
                    $authManager->verifyEmailPin($request),
                    Response::HTTP_OK
                );
            } catch (WrongPinException $exception) {
                return new JsonResponse([
                    'message' => $exception->getMessage(),
                    'errors'  => [
                        'message' => $exception->getMessage(),
                        'code'    => $exception->getCode()
                    ]
                ], Response::HTTP_BAD_REQUEST);
            }
        }
    }

    public function phoneConfirmation(PhoneConfirmRequest $request, AuthManager $authManager)
    {
        if ($request->validated()) {
            try {
                return new JsonResponse(
                    $authManager->phoneConfirmation($request->get('idToken')),
                    Response::HTTP_OK
                );
            } catch (PermissionDeniedException $exception) {
                return new JsonResponse([
                    'errors' => [
                        'message' => $exception->getMessage(),
                        'code'    => $exception->getCode(),
                    ], Response::HTTP_UNAUTHORIZED
                ]);
            }
        }
    }

    public function getTutorialImages() {
        $tutorial_setting_config = ConfigEntity::where('path', 'tutorial_setting')->first();
        $tutorial_setting_value = json_decode($tutorial_setting_config->value, true);

        $disk = \Storage::disk('gcs');

        $tutorial_setting_value_ = [];
        foreach ($tutorial_setting_value as $item) {
            if($item['image'] && !$item['disabled'])
                array_push($tutorial_setting_value_, [
                    'image' => $item['image'] ? $disk->url($item['image']) : null,
                    'title' => $item['title'],
                    'description' => $item['description'],
                    'disabled' => $item['disabled'],
                ]);
        }
        return new JsonResponse( fractal($tutorial_setting_value_, new TutorialSettingTransform()), Response::HTTP_OK);
    }

    public function inviteByContactList(Request $request) {

        $current_user = auth()->user();

        $contact_emails = $request->has('contact_emails') ? $request->contact_emails : [];
        foreach ($contact_emails as $contact_email ) {
            $user = User::where('email', $contact_email)->first();

            if(!$user) {
                $subject = 'Kizuner invitation';
                $greeting = '<p><strong>Hello, ' . $contact_email . '</strong></p>';

                $to = $contact_email;
                $data['from'] = 'noreply@kizuner.com';
                $data['sender'] = 'Kizuner';
                $body_content = $greeting;
                $body_content .= '<p>'. $current_user->name .' would like to invite you join to Kizuner.</p>';
                $body_content .= '<p>'. $request->share_url . '</p>';

                $data['mail_content'] = $body_content;
                SendMailService::sendMail('mail-templates.mail-template', $to, $subject, $data);

                /*
                $mail = (new MailMessage)
                    ->from('noreply@kizuner.com')
                    ->cc($contact_email)
                    ->subject($subject)
                    ->greeting($greeting)
                    ->line($line1)
                    ->line('Meet you on Kizuner.');
                */
            }
        }

        return new JsonResponse([
            'data' => [
                'message' => 'Email invites was sent.',
                'status'  => true
            ]
        ]);
    }
}
