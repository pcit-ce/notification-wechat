<?php

declare(strict_types=1);

namespace PCIT\Notification\WeChat;

use App\Build;
use App\Repo;
use PCIT\Framework\Support\Date;
use PCIT\Notification\Interfaces\HandlerInterface;
use PCIT\PCIT;
use PCIT\Support\Git;

class Handler implements HandlerInterface
{
    /**
     * @throws \Exception
     */
    public static function send(int $build_key_id, string $info): void
    {
        $pcit = new PCIT();

        $result = Build::find($build_key_id);

        list(
            'build_status' => $build_status,
            'finished_at' => $time,
            'event_type' => $event_type,
            'rid' => $rid,
            'branch' => $branch,
            'commit_message' => $commit_message,
            'committer_username' => $committer_username,
            'git_type' => $git_type,
            'commit_id' => $commit_id
            ) = $result;

        $repo_full_name = Repo::getRepoFullName((int) $rid, $git_type);

        $result = $pcit->wechat_template_message->sendTemplateMessage(
            $build_status,
            Date::Int2ISO((int) $time),
            $event_type,
            $repo_full_name,
            $branch,
            substr($commit_message, 0, 60),
            $committer_username,
            $info,
            Git::getCommitUrl($git_type, $repo_full_name, $commit_id)
        );

        \Log::debug($result);
    }
}
