<?php
/*

require 'vendor/autoload.php';

use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;

class TextToSpeechService
{
    public static function generateAudio($text, $articleId)
    {
        putenv('GOOGLE_APPLICATION_CREDENTIALS=config/google-cloud-key.json');

        $filePath = "../../web/uploads/audio/article_" . $articleId . ".mp3";

        // Kontrola, zda se obsah článku změnil (porovná aktuální text s již uloženým souborem)
        if (file_exists($filePath)) {
            $currentHash = md5($text);
            $storedHash = md5(file_get_contents($filePath));

            if ($currentHash === $storedHash) {
                return $filePath; // Pokud se text nezměnil, neprováděj znovu generování
            }
        }

        $client = new TextToSpeechClient();

        $input = new SynthesisInput();
        $input->setText($text);

        $voice = new VoiceSelectionParams();
        $voice->setLanguageCode('cs-CZ');
        $voice->setSsmlGender(1); // 1 = MALE, 2 = FEMALE

        $audioConfig = new AudioConfig();
        $audioConfig->setAudioEncoding(AudioEncoding::MP3);

        $response = $client->synthesizeSpeech($input, $voice, $audioConfig);
        file_put_contents($filePath, $response->getAudioContent());

        $client->close();
        return $filePath;
    }     
}

*/