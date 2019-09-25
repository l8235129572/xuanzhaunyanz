# xuanzhaunyanz
拖动滑块旋转图片，完整人机验证。仿百度旋转验证码，拷贝百度验证码html部分。其本身存在严重bug,需要超大图库才能防止人工智能破解图像旋转角度。
# thinkphp5.x 调用
[Yzt.php](https://github.com/scupte/xuanzhaunyanz/blob/master/Yzt.php "Yzt.php") 文件和 [2.ttf](https://github.com/scupte/xuanzhaunyanz/blob/master/2.ttf "2.ttf")
放在extend/Yzt 目录下。
```php
<?php
namespace app\api\controller;
use think\Controller;
use Yzt\Yzt;
class Yzpic extends Controller
{
    public function index()
    {
        $Yzt = new \Yzt\Yzt('https://api.uomg.com/api/rand.img1', true, true, rand(20, 270));
       // $info = $Yzt->move('static/upload/');
        //return $info;
        //die();
        header("Content-type: image/png;text/html; charset=utf-8");
        $tot = rand(20, 300);
        imagepng($Yzt->changeCircularImg());
        die();

    }
}
```
