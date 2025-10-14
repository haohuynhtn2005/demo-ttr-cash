<?php

namespace App\Requests;

class EmailHistoryRequest {
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public static function rules(): array {
    return [
      'code'          => 'permit_empty|string|max_length[100]',
      'recipient'     => 'required|valid_email|max_length[255]',
      'cc'            => 'permit_empty|valid_emails|max_length[255]',
      'bcc'           => 'permit_empty|valid_emails|max_length[255]',
      'subject'       => 'required|string|max_length[255]',
      'body'          => 'required|string',
      'error_message' => 'permit_empty|string',
      'sent_at'       => 'permit_empty|valid_date',
      'resent_times'  => 'permit_empty|integer',
    ];
  }

  /**
   * Get the validation messages that apply to the request.
   *
   * @return array
   */
  public static function messages(): array {
    return [
      'recipient' => [
        'required'    => 'The recipient email is required.',
        'valid_email' => 'The recipient must be a valid email address.',
      ],
      'subject' => [
        'required' => 'The email subject is required.',
      ],
      'body' => [
        'required' => 'The email body is required.',
      ],
    ];
  }
}
