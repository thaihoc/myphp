Ví dụ upload tệp tin:

+ Thuộc tính name của phần tử cần upload: P_FILE
+ Thư mục lưu trữ tệp tin upload: avatar

$uploader = new \Nth\File\Upload\Uploader(CFG_UPLOAD_FOLDER . 'avatar', 'P_FILE');

//Giới hạn tệp tin được phép uploaded
$uploader->getValidator()->setExtensions(CFG_UPLOAD_EXTENSIONS_ACCEPT);

//Lấy kết quả trả về sau khi upload
$result = $uploader->upload();

