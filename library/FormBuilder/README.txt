FormBuilder constructions:

Các loại phần tử hỗ trợ: Text, File, Select, Date, Textarea, Captcha, Checkbox.
Ví dụ cách thức khai báo chung: 

Cách 1: Khai báo trực tiếp từ lớp của loại phần tử muốn sử dụng

$text = new Text('usename' //Tự động lấy giá trị này làm id và name nếu chúng không được khai báo trong tham số attributes
    	, array('autofocus' => true) //Array attributes
    	, array('label' => 'Tên đăng nhập')); // Array options (Xem phần hướng dẫn sử dụng các options bên dưới)

Cách 2: Khai báo thông qua lớp Element

$text = new Element(Element::TEXT
    	, 'username'
    	, array('autofocus' => true) //Array attributes
    	, array('label' => 'Tên đăng nhập')); //Array options

Cách 3: Khai báo từ một phương thức tĩnh của lớp Element

$text = Element::create(Element::TEXT
    	, 'username'
    	, array('autofocus' => true) //Array attributes
    	, array('label' => 'Tên đăng nhập')); //Array options

FormBuilder options: Các tùy biến của FormBuilder plugin

label: Tên của thành phần label cho một phần tử của form, áp dụng cho tất cả các phần tử. Mặc định là NULL

value: Đặt giá trị thiết lập cho phần tử File. (Được phân cách bởi dấu :). Mặc định là NULL

marksMandatory: Cấu hình hiển thị nội dung (*) đi kèm với label khi giá trị trên các phần tử là bắt buộc. Lưu ý: Nếu thuộc tính valid-required=1 giá trị này được đặt mặc định là true.

wrapperAttributes: Cấu hình các thuộc tính của thành phần wrapper của phần tử trong form, áp dụng cho tất cả các phần từ. Mặc định là NULL

controlOnly: Nếu giá trị là true thì kết quả trả về của hàm toString chỉ là nội dung của thành phần control của phần tử trong form, áp dụng cho tất cả các loại phần tử. Mặc định là false

showLabel: Cấu hình hiển thị thành phần label của phần tử. Mặc định là true

selected : Đặt giá trị được chọn cho phần tử select khi nó được thiết lập. Mặc định là NULL

innerHtml : Nội dung bên trong của thành phần control của phần tử trong form, áp dụng cho tất cả các loại phần tử. Mặc định là NULL

bindOptions : Cho phép thiết lập các select items trên phần tử select từ một mảng dữ liệu đầu vào. Mặc định là []
	+ name : tên của key để hiển thị text item. Mặc định là NULL
        + value : tên của key để đặt giá trị cho item. Mặc định là NULL
        + data : mảng dữ liệu (Vd: mảng trả về sau khi gọi một hàm select từ database). Mặc định là NULL
        + selected : Đặt giá trị được chọn khi nó được thiết lập (Có thể sử dụng selected option bên trên). Mặc định là NULL
        + emptyOption : Cấu hình nội dung của item rỗng hiển thị trên cùng trong danh sách các items (Vd: -- Chưa chọn --, Tất cả, ...). Mặc định là NULL

checked : Đặt giá trị để kiểm tra check hoặc uncheck trên phần tử Checkbox hoặc Radio. Mặc định là NULL

fileList : Cấu hình các đặt trưng cho danh sách tệp tin hiển thị của phần tử File. Mặc định là []
        + show : Cho phép hiển thị hoặc không hiển thị danh sách tệp tin. Mặc định là true
        + removable : Cho phép hiển thị hoặc không hiển thị chức năng xóa tệp tin. Mặc định là true
        + aAttributes : Cấu hình các thuộc tính HTML của thẻ a trong một item. Mặc định là []
        + emptyText: Cấu hình văn bản hiển thị khi danh sách rỗng. Mặc định là NULL
