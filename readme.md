# Juice Attachments Package

Provide attachment controller and just use it in your application routes. We take care rest of things.

## Installation

1. run composer require command `composer require juice/attachments`

2. register `\Juice\Attachments\AttachmentsServiceProvider::class` service provider

3. copy config file and set it up

   - Laravel - `php artisan vendor:publish --provider="Juice\Attachments\AttachmentsServiceProvider"`

   - Lumen - `cp vendor/juice/attachments/config/juice-attachments.php config/`

     (make sure config directory exist)

4. run setup command `php artisan attachment:setup`

5. run database migration `php artisan migrate`

6. setup your application routes

7. done

## Controller Methods

＊：Assume namespace is `\Juice\Attachments\Controllers`

### Upload Attachments

- end point

  `AttachmentController@upload`

- method parameters

  none

- query string

  none

- form data

  |   field   |      type      | required |
  | :-------: | :------------: | :------: |
  | ja_file[] | array of files |    ✓     |

- return value

  array of successfully uploaded files' name, e.g.

  ```json
  ["sjdwd.png", "pweog.pdf", "msptw.jpeg"]
  ```

- route example

  `Route::post('/attachments', 'AttachmentController@upload')`

### Download Attachment

- end point

  `AttachmentController@download`

- method parameters

  |   field   |  type  | required |
  | :-------: | :----: | :------: |
  | $filename | string |    ✓     |

- query string

  | field |  type   | required | default |               remark                |
  | :---: | :-----: | :------: | :-----: | :---------------------------------: |
  |   d   | boolean |          |    0    | true: attachment<br />false: inline |

- form data

  none

- return value

  `Symfony\Component\HttpFoundation\BinaryFileResponse`

- route example

  `Route::get('/attachments/{id}', 'AttachmentController@download'); // https://example.com/attachments/sjdwd.png`

### Trash Attachment

- end point

  `AttachmentController@trash`

- method parameters

  |   field   |  type  | required |
  | :-------: | :----: | :------: |
  | $filename | string |    ✓     |

- query string

  none

- form data

  none

- return value

  json response contain success key, e.g.

  ```json
  {
      "success": true
  }
  ```

- route example

  `Route::delete('/attachments/{id}', 'AttachmentController@trash'); // https://example.com/attachments/sjdwd.png`