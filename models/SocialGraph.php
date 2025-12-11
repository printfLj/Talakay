<?php

require_once __DIR__ . '/../includes/data.php';

class SocialGraph
{
    private string $friendsFile;
    private string $messagesFile;
    private string $usersFile;

    public function __construct(
        string $friendsFile = __DIR__ . '/../data/friends.json',
        string $messagesFile = __DIR__ . '/../data/messages.json',
        string $usersFile = __DIR__ . '/../data/users.json'
    ) {
        $this->friendsFile = $friendsFile;
        $this->messagesFile = $messagesFile;
        $this->usersFile = $usersFile;
    }

    /**
     * Check if a user email exists in the users database
     */
    private function userExists(string $email): bool
    {
        $users = load_json($this->usersFile, []);
        foreach ($users as $user) {
            if ($user['email'] === $email) {
                return true;
            }
        }
        return false;
    }

    public function getFriends(string $email): array
    {
        $all = load_json($this->friendsFile, []);
        return $all[$email]['friends'] ?? [];
    }

    /**
     * Add a friend - only if the friend email exists as a registered user
     * Returns: true if successful, false if user doesn't exist or same email
     */
    public function addFriend(string $userEmail, string $friendEmail): bool
    {
        // Prevent adding yourself
        if ($userEmail === $friendEmail) {
            return false;
        }

        // Check if the friend email exists in the users database
        if (!$this->userExists($friendEmail)) {
            return false;  // Friend email doesn't exist as a registered user
        }

        $all = load_json($this->friendsFile, []);

        $all[$userEmail]['friends'] = $all[$userEmail]['friends'] ?? [];
        $all[$friendEmail]['friends'] = $all[$friendEmail]['friends'] ?? [];

        if (!in_array($friendEmail, $all[$userEmail]['friends'], true)) {
            $all[$userEmail]['friends'][] = $friendEmail;
        }

        if (!in_array($userEmail, $all[$friendEmail]['friends'], true)) {
            $all[$friendEmail]['friends'][] = $userEmail;
        }

        return save_json($this->friendsFile, $all);
    }

    public function getConversation(string $a, string $b): array
    {
        $messages = load_json($this->messagesFile, []);
        $key = $this->conversationKey($a, $b);
        return $messages[$key] ?? [];
    }

    public function sendMessage(string $from, string $to, string $body): bool
    {
        if (trim($body) === '') {
            return false;
        }
        $messages = load_json($this->messagesFile, []);
        $key = $this->conversationKey($from, $to);

        $messages[$key] = $messages[$key] ?? [];
        $messages[$key][] = [
            'id' => generate_id('msg'),
            'from' => $from,
            'to' => $to,
            'body' => $body,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        return save_json($this->messagesFile, $messages);
    }

    private function conversationKey(string $a, string $b): string
    {
        $pair = [$a, $b];
        sort($pair, SORT_STRING);
        return implode('::', $pair);
    }
}

