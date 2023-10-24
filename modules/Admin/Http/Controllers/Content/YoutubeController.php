<?php

namespace Modules\Admin\Http\Controllers\Content;

class YoutubeController
{

    public function show()
    {
        $url  = app('request')->input('url');
        $api_key = 'AIzaSyDkQezPy7OG_xMLCtdW50NOCuTf-3WRRq0';
        $apiUrl = 'https://www.googleapis.com/youtube/v3/videos?part=snippet%2CcontentDetails%2Cstatistics&id=';
        $api_url = $apiUrl . $this->getYouTubeVideoID($url) . '&key=' . $api_key;
        return (array)(file_get_contents($api_url));
    }


    private function getYouTubeVideoID($url) {
        $queryString = parse_url($url, PHP_URL_QUERY);
        parse_str($queryString, $params);
        if (isset($params['v']) && strlen($params['v']) > 0) {
            return $params['v'];
        } else {
            return "";
        }
    }
}
