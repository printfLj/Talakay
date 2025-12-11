<?php

require_once __DIR__ . '/../includes/data.php';

class PostRepository
{
    private string $file;

    public function __construct(string $file = __DIR__ . '/../data/posts.json')
    {
        $this->file = $file;
    }

    public function all(): array
    {
        return load_json($this->file, []);
    }

    public function search(?string $query = null, ?string $tag = null, ?string $topic = null): array
    {
        $posts = $this->all();
        return array_values(array_filter($posts, function ($post) use ($query, $tag, $topic) {
            $matchesQuery = true;
            $matchesTag = true;
            $matchesTopic = true;

            if ($query) {
                $q = strtolower($query);
                $haystack = strtolower(
                    ($post['title'] ?? '') . ' ' .
                    ($post['body'] ?? '') . ' ' .
                    ($post['author'] ?? '') . ' ' .
                    ($post['location'] ?? '')
                );
                $matchesQuery = strpos($haystack, $q) !== false;
            }

            if ($tag) {
                $matchesTag = in_array(strtolower($tag), array_map('strtolower', $post['tags'] ?? []), true);
            }

            if ($topic) {
                $matchesTopic = ($post['topic'] ?? '') === $topic;
            }

            return $matchesQuery && $matchesTag && $matchesTopic;
        }));
    }

    public function addPost(array $data): array
    {
        $posts = $this->all();
        $post = [
            'id' => generate_id('post'),
            'author' => $data['author'] ?? 'Anonymous',
            'author_email' => $data['author_email'] ?? null,
            'location' => $data['location'] ?? '',
            'topic' => $data['topic'] ?? 'general',
            'tags' => $this->normalizeTags($data['tags'] ?? []),
            'title' => $data['title'] ?? '',
            'body' => $data['body'] ?? '',
            'created_at' => date('Y-m-d H:i:s'),
            'replies' => [],
        ];
        $posts[] = $post;
        save_json($this->file, $posts);
        return $post;
    }

    public function addReply(string $postId, array $data): ?array
    {
        $posts = $this->all();
        foreach ($posts as &$post) {
            if ($post['id'] === $postId) {
                $reply = [
                    'id' => generate_id('reply'),
                    'parent_id' => $data['parent_id'] ?? null,
                    'author' => $data['author'] ?? 'Neighbor',
                    'author_email' => $data['author_email'] ?? null,
                    'body' => $data['body'] ?? '',
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                $post['replies'][] = $reply;
                save_json($this->file, $posts);
                return $reply;
            }
        }
        return null;
    }

    public function deletePost(string $postId, ?string $userEmail = null): bool
    {
        $posts = $this->all();
        foreach ($posts as $index => $post) {
            if ($post['id'] === $postId) {
                // Only allow deletion if userEmail matches the post author or if no userEmail specified
                if ($userEmail === null || $post['author_email'] === $userEmail) {
                    unset($posts[$index]);
                    return save_json($this->file, array_values($posts));
                }
                return false; // User does not own this post
            }
        }
        return false; // Post not found
    }

    public function deleteReply(string $postId, string $replyId, ?string $userEmail = null): bool
    {
        $posts = $this->all();
        foreach ($posts as &$post) {
            if ($post['id'] === $postId) {
                foreach ($post['replies'] as $index => $reply) {
                    if ($reply['id'] === $replyId) {
                        // Only allow deletion if userEmail matches the reply author or if no userEmail specified
                        if ($userEmail === null || $reply['author_email'] === $userEmail) {
                            unset($post['replies'][$index]);
                            $post['replies'] = array_values($post['replies']);
                            return save_json($this->file, $posts);
                        }
                        return false; // User does not own this reply
                    }
                }
            }
        }
        return false; // Post or reply not found
    }

    private function normalizeTags($tags): array
    {
        if (is_string($tags)) {
            $tags = explode(',', $tags);
        }
        $tags = array_map(function ($tag) {
            return trim(ltrim($tag, '#'));
        }, $tags);
        $tags = array_filter($tags, fn($t) => $t !== '');
        return array_values(array_unique($tags));
    }
}

