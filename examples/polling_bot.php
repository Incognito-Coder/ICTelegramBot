<?php
/**
 * Example 2: Long Polling Bot (CLI)
 *
 * Run this directly from command line (ideal for local testing):
 * php examples/polling_bot.php
 */

require_once __DIR__ . '/../ICTelegramBot.php';

use TelegramBot\ICBot;

$token = 'YOUR_BOT_TOKEN'; // Replace with your bot token
$bot = new ICBot($token);

// Optional: Configure proxy if needed
// $bot->Initialize($token, '127.0.0.1:1080', null, 'SOCKS5');

echo "Starting Long Polling Bot... Press Ctrl+C to stop.\n";

// Remove existing webhook so getUpdates works
$bot->DeleteWebHook();

$offset = 0;

while (true) {
    // Request updates with 30s timeout
    $updates = $bot->GetUpdates([
        'offset'  => $offset,
        'timeout' => 30,
        'limit'   => 100,
    ]);

    if (!empty($updates->ok) && !empty($updates->result)) {
        foreach ($updates->result as $update) {
            // Move offset to confirm receipt of this update
            $offset = $update->update_id + 1;

            // Load the update into ICBot
            $bot->SetUpdate((array)$update);

            $chatId = $bot->GetChatID();
            $text   = $bot->GetText();

            echo "Received update #{$update->update_id} in chat {$chatId}\n";

            if ($text === '/ping') {
                $bot->SendMessage($chatId, '🏓 Pong!');
            } elseif ($text === '/dice') {
                $bot->SendDice($chatId, ICBot::DICE_DEFAULT);
            }
        }
    }

    // Sleep briefly to avoid aggressive looping on errors
    usleep(200000);
}
