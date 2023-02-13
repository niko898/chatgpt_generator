<?php
class ModelToolChatgptGenerator extends Model {
    public $debug = 1;

    public function generate($prompt){
        $dTemperature = (float)$this->config->get('module_chatgpt_generator_temperature');
        $iMaxTokens = (int)$this->config->get('module_chatgpt_generator_max_tokens');
        $top_p = (float)$this->config->get('module_chatgpt_generator_top_p');;
        $frequency_penalty = (float)$this->config->get('module_chatgpt_generator_frequency_penalty');
        $presence_penalty = (float)$this->config->get('module_chatgpt_generator_presence_penalty');
        $OPENAI_API_KEY = $this->config->get('module_chatgpt_generator_api_key');
        $sModel = $this->config->get('module_chatgpt_generator_model');
        $headers  = [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $OPENAI_API_KEY . ''
        ];

        $stop = "\n";
        if($this->config->get('module_chatgpt_generator_stop')){
            $stop = explode(',', $this->config->get('module_chatgpt_generator_stop'));
        }
        $postData = [
            'model' => $sModel,
            'prompt' => str_replace('"', '', $prompt),
            'temperature' => $dTemperature,
            'max_tokens' => $iMaxTokens,
            'top_p' => $top_p,
            'frequency_penalty' => $frequency_penalty,
            'presence_penalty' => $presence_penalty,
            'stop' => json_encode($stop),
        ];

        $result = $this->request('https://api.openai.com/v1/completions', $headers, $postData);
        return $result;
    }


    public function request($url, $header = array(), $data = array()){
        if(!$header){
            $header = array('Content-Type:application/x-www-form-urlencoded');
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 120);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        $out = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $code = (int) $code;
        $errors = array(
            400 => 'Bad request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not found',
            500 => 'Internal server error',
            502 => 'Bad gateway',
            503 => 'Service unavailable',
        );
        if ($code < 200 && $code > 204) {
            $this->log("We got some error $code: " . $errors[$code]);
            return false;
        }
        $response = json_decode($out, true);
        $this->log("Answer from chatGPT - $url - [$code]: " . print_r($response, true));
        return $response;
    }

    public function log($message){
        if(!$this->debug){
            return;
        }
        if (file_exists(DIR_LOGS . 'chatgpt_generator.log') && filesize(DIR_LOGS . 'chatgpt_generator.log') >= 100 * 1024 * 1024) {
            unlink(DIR_LOGS . 'chatgpt_generator.log');
        }
        if($this->debug){
            file_put_contents(DIR_LOGS . 'chatgpt_generator.log', date("Y-m-d H:i:s - ") . ": " . $message . "\r\n", FILE_APPEND);
        }
    }
}