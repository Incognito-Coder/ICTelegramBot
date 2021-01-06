<?php

use TelegramBot\ICBot;

$Connect = new mysqli('localhost', 'mralirez_mralirez', 'incognito@2020', 'mralirez_mediabot');
require_once('ICTelegramBot.php');
define('API_KEY', "849019242:AAHYaf9f3mg0eQVBHBiVRswyB8s-Rc8Dq2g");
$bot = new ICBot();
$bot->Initialize(API_KEY);
$text = $bot->GetText();
$chat_id = $bot->GetChatID();
$username = $bot->GetUsername();
$caption = $bot->GetCaption();
$msgid = $bot->MessageID();
function RandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

if ($text == "/start") {
    $bot->SendMessage($chat_id, "Hello @$username\nUse me to save your files.\nDeveloper : @Incognito_Coder", "html", true, null, null, $bot->InlineKeyboard("Channel", "tg://resolve?domain=ic_mods"));
} elseif ($text == '/about') {
    $bot->SendMessage($chat_id, "Developer : Incognito Coder\nCode Language : php\nUsed Libraries : ICTelegramBot , MySQL\nGithub : https://github.com/Incognito-Coder", "html", false, null, null, $bot->InlineKeyboard("Contact ME", "tg://resolve?domain=icrobot"));
} elseif ($bot->MessageType() == "music") {
    $id = $bot->GetFileID('audio');
    $unique = RandomString();
    $bot->SendMessage($chat_id, "Status : File Saved Successfully.\nLINK: https://t.me/MediaTransferBot?start=file-$unique");
    $sql = "INSERT INTO files (file_id,type,unique_id) VALUES ('$id','audio','$unique')";
    if ($Connect->query($sql) === TRUE) {
        $bot->SendMessage($chat_id, "*New record created successfully.*", 'markdown');
    } else {
        $bot->SendMessage($chat_id, "Error: " . $sql . "\n" . $Connect->error);
    }
    $Connect->close();
} elseif ($bot->MessageType() == "document") {
    $id = $bot->GetFileID('document');
    $unique = RandomString();
    $bot->SendMessage($chat_id, "Status : File Saved Successfully.\nLINK: https://t.me/MediaTransferBot?start=file-$unique");
    $sql = "INSERT INTO files (file_id,type,unique_id) VALUES ('$id','document','$unique')";
    if ($Connect->query($sql) === TRUE) {
        $bot->SendMessage($chat_id, "*New record created successfully.*", 'markdown');
    } else {
        $bot->SendMessage($chat_id, "Error: " . $sql . "\n" . $Connect->error);
    }
    $Connect->close();
} elseif ($bot->MessageType() == "video") {
    $id = $bot->GetFileID('video');
    $unique = RandomString();
    $bot->SendMessage($chat_id, "Status : File Saved Successfully.\nLINK: https://t.me/MediaTransferBot?start=file-$unique");
    $sql = "INSERT INTO files (file_id,type,unique_id) VALUES ('$id','video','$unique')";
    if ($Connect->query($sql) === TRUE) {
        $bot->SendMessage($chat_id, "*New record created successfully.*", 'markdown');
    } else {
        $bot->SendMessage($chat_id, "Error: " . $sql . "\n" . $Connect->error);
    }
    $Connect->close();
} elseif ($bot->MessageType() == "photo") {
    $id = $bot->GetFileID('photo');
    $unique = RandomString();
    $bot->SendMessage($chat_id, "Status : File Saved Successfully.\nLINK: https://t.me/MediaTransferBot?start=file-$unique");
    $sql = "INSERT INTO files (file_id,type,unique_id) VALUES ('$id','photo','$unique')";
    if ($Connect->query($sql) === TRUE) {
        $bot->SendMessage($chat_id, "*New record created successfully.*", 'markdown');
    } else {
        $bot->SendMessage($chat_id, "Error: " . $sql . "\n" . $Connect->error);
    }
    $Connect->close();
} elseif (strpos($text, '/file ') !== false) {
    $txt = str_replace('/file ', '', $text);
    $sql = "SELECT * FROM files WHERE id = '$txt'";
    $result = $Connect->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $bot->SendMessage($chat_id, "id: " . $row["id"] . "\n" . "file: " . $row["file_id"]);
        }
    } else {
        $bot->SendMessage($chat_id, "*No Query Found*", 'markdown');
    }
    $Connect->close();
} elseif (strpos($text, '/start file-') !== false) {
    $split = explode('-', $text);
    $final = $split[1];
    $sql = "SELECT * FROM files WHERE unique_id = '$final'";
    $result = $Connect->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($row['type'] == 'video') {
                $bot->SendVideo($chat_id, $row["file_id"], "_Uploaded by_ [Media Transfer](tg://resolve?domain=MediaTransferBot)", 'markdown');
            } elseif ($row['type'] == 'audio') {
                $bot->SendAudio($chat_id, $row["file_id"], "_Uploaded by_ [Media Transfer](tg://resolve?domain=MediaTransferBot)", 'markdown');
            } elseif ($row['type'] == 'document') {
                $bot->SendDocument($chat_id, $row["file_id"], "_Uploaded by_ [Media Transfer](tg://resolve?domain=MediaTransferBot)", 'markdown');
            } elseif ($row['type'] == 'photo') {
                $bot->SendPhoto($chat_id, $row["file_id"], "_Uploaded by_ [Media Transfer](tg://resolve?domain=MediaTransferBot)", 'markdown');
            }
        }
    } else {
        $bot->SendMessage($chat_id, "*No Query Found*", 'markdown');
    }
} else {
    $bot->SendMessage($chat_id, "_No command._", 'markdown', null, null, $msgid, null);
}
