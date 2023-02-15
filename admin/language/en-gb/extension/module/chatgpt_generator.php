<?php
// Heading
$_['heading_title']    = 'ChatGPT plugin from Galocaffe';

// Text
$_['text_extension']   = 'Extensions';
$_['text_success']     = 'Success: You have modified module!';
$_['text_edit']        = 'ChatGPT plugin Settings';
$_['text_test_section']        = 'Test section';

// Buttons
$_['button_test']   = 'Send request';
$_['button_add_to_list']   = 'Add products with empty description to work list';
$_['button_add_all_products']   = 'Add all products to work list';
$_['button_add_to_bad_list']   = 'Add Bad products to work list';
$_['button_clear_products']        = 'Clear the work list';
$_['button_manual_add_to_list']        = 'Add these products to work list';
$_['button_test_start']        = 'Start generation (only for few products, please use the cron)';
$_['button_clear_bad_products']        = 'Clear the bad list';

// Entry
$_['entry_status']     = 'Status';
$_['entry_debug_status']     = 'Debug status';
$_['entry_product_description_status']     = 'Generate product description';
$_['entry_api_key']     = 'ChatGPT Api key';
$_['entry_prompt']     = 'Prompt';
$_['entry_temperature']     = 'Temperature';
$_['entry_max_tokens']     = 'Max tokens';
$_['entry_top_p']     = 'Top_p';
$_['entry_presence_penalty']     = 'Presence penalty';
$_['entry_frequency_penalty']     = 'Frequency penalty';
$_['entry_model']     = 'Model';
$_['entry_stop']     = 'Stop (Comma separated)';
$_['entry_test']     = 'Testing';
$_['entry_languages']     = 'Languages';
$_['entry_product_work_count']     = 'IMPORTANT! Add the products to work list';
$_['entry_bad_product_work_count']     = 'Bad products list';
$_['entry_cron']     = 'Cron task command';
$_['entry_manual']     = 'Add manual products';


// Help
$_['help_temperature']     = 'What sampling temperature to use, between 0 and 2. Higher values like 0.8 will make the output more random, while lower values like 0.2 will make it more focused and deterministic.
We generally recommend altering this or top_p but not both.';
$_['help_max_tokens']     = 'The maximum number of tokens to generate in the completion.
The token count of your prompt plus max_tokens cannot exceed the model\'s context length. Most models have a context length of 2048 tokens (except for the newest models, which support 4096).';
$_['help_top_p']     = 'An alternative to sampling with temperature, called nucleus sampling, where the model considers the results of the tokens with top_p probability mass. So 0.1 means only the tokens comprising the top 10% probability mass are considered.
We generally recommend altering this or temperature but not both.';
$_['help_presence_penalty']     = 'Number between -2.0 and 2.0. Positive values penalize new tokens based on whether they appear in the text so far, increasing the model\'s likelihood to talk about new topics.';
$_['help_frequency_penalty']     = 'Number between -2.0 and 2.0. Positive values penalize new tokens based on their existing frequency in the text so far, decreasing the model\'s likelihood to repeat the same line verbatim.';
$_['help_model']     = 'ID of the model to use.';
$_['help_stop']     = 'Up to 4 sequences where the API will stop generating further tokens. The returned text will not contain the stop sequence.';
$_['help_languages']     = 'Languages which module will work (The lanuage will add to the prompt)';
$_['help_product_work_count']     = 'Here you should to add products to work list, you can add all products or only where are empty descriptions. (Without this list the generator won`t work, the count must be more than 0)';
$_['help_bad_product_work_count']     = 'This list contain count of bad products, bad products this is when during generation the module got some error - this product will add to bad lists automatically';
$_['help_manual']     = 'With this field you can add the product list what will be generated';
$_['help_prompt']     = 'This is the main prompt to ChaGPT, you can use tags : {product_name}, {product_model}, {product_sku}, {product_ean}';

// Error
$_['error_permission'] = 'Warning: You do not have permission to modify account module!';
$_['error_api_key'] = 'The ChatGPT Api key field is required';
$_['error_temperature'] = 'The ' . $_['entry_temperature'] . ' field is required';
$_['error_max_tokens'] = 'The ' . $_['entry_max_tokens'] . ' field is required';
$_['error_top_p'] = 'The ' . $_['entry_top_p'] . ' field is required';
$_['error_presence_penalty'] = 'The ' . $_['entry_presence_penalty'] . ' field is required';
$_['error_frequency_penalty'] = 'The ' . $_['entry_frequency_penalty'] . ' field is required';
$_['error_model'] = 'The ' . $_['entry_model'] . ' field is required';
$_['error_stop'] = 'The ' . $_['entry_stop'] . ' field is required';