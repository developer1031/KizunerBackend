<?php

namespace Modules\Admin\Http\Controllers\Content;

use Illuminate\Http\Request;
use Modules\Config\ConfigEntity;

class TermPrivacyController
{
    public function index()
    {
        $term = ConfigEntity::where('path', 'term')->first();
        $policy = ConfigEntity::where('path', 'policy')->first();
        $faq = ConfigEntity::where('path', 'faq')->first();
        $about = ConfigEntity::where('path', 'about')->first();

        if (!$term) {
            $term = new ConfigEntity();
            $term->path = 'term';
            $term->save();
        }

        if (!$faq) {
            $term = new ConfigEntity();
            $term->path = 'faq';
            $term->save();
        }

        if (!$policy) {
            $policy = new ConfigEntity();
            $policy->path = 'policy';
            $policy->save();
        }

        if (!$about) {
            $about = new ConfigEntity();
            $about->path = 'about';
            $about->save();
        }

        return view('content::term.index')->with([
            'term'      => $term,
            'policy'    => $policy,
            'faq'       => $faq,
            'about'     => $about
        ]);
    }

    public function update(Request $request)
    {
        $term = ConfigEntity::where('path', 'term')->first();
        $term->value = $request->term;
        $term->save();

        $term = ConfigEntity::where('path', 'faq')->first();
        $term->value = $request->faq;
        $term->save();

        $policy = ConfigEntity::where('path', 'policy')->first();
        $policy->value = $request->policy;
        $policy->save();

        $about = ConfigEntity::where('path', 'about')->first();
        $about->value = $request->about;
        $about->save();

        return redirect()->back()->withSuccess("Update Term and Condition successful");
    }
}
