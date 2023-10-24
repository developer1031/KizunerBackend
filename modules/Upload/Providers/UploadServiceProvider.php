<?php

namespace Modules\Upload\Providers;

use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Superbalist\Flysystem\GoogleStorage\GoogleStorageAdapter;

class UploadServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        \Storage::extend('gcs', function ($app, $config) {
            $storageClient = new StorageClient([
                'projectId' => $config['project_id'],
                'keyFilePath' => base_path('credentials.json')
            ]);

            $bucket = $storageClient->bucket($config['bucket']);
            $pathPrefix = Arr::get($config, 'path_prefix');
            $storageApiUri = Arr::get($config, 'storage_api_uri');

            $adapter = new GoogleStorageAdapter($storageClient, $bucket, $pathPrefix, $storageApiUri);
            return new Filesystem($adapter, ['visibility' => 'public']);
        });
    }
}
