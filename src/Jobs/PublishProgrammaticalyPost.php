<?php

namespace Guysolamour\Administrable\Jobs;

use Carbon\Carbon;
use Illuminate\Foundation\Bus\Dispatchable;

class PublishProgrammaticalyPost
{
    use Dispatchable;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // select post
        $now = Carbon::now();

        /** @var \Illuminate\Support\Collection */
        $posts = config('administrable.extensions.blog.post.model')::programmaticaly()
            ->whereDate('published_at', $now)
            ->whereRaw("HOUR(`published_at`) = $now->hour")
            ->get();

        // stop if post not found
        if (!$posts) {
            return;
        }

        // publish the post
        $posts->each->publishProgrammaticaly();
    }
}
