# Juice Attachments Package



## Controller Methods

### Upload Attachments

- end point
  `\Juice\Attachments\Controllers\AttachmentController@upload`

- method parameters
  none

- query string
  none

- form data

  |   field   |      type      | required |
  | :-------: | :------------: | :------: |
  | ja_file[] | array of files |    ✓     |

- return value
  array of successfully uploaded files' name
  e.g.

  ```json
  ['sjdwd.png', 'pweog.pdf', 'msptw.jpeg']
  ```

### Download Attachment

- end point
  `\Juice\Attachments\Controllers\AttachmentController@download`

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
  BinaryFileResponse

### Trash Attachment

- end point
  `\Juice\Attachments\Controllers\AttachmentController@trash`

- method parameters

  |   field   |  type  | required |
  | :-------: | :----: | :------: |
  | $filename | string |    ✓     |

- query string
  none

- form data
  none

- return value
  json response contain success key

  e.g.

  ```json
  {
      "success": true
  }
  ```
