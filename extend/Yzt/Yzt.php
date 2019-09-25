<?php
namespace Yzt;

class Yzt
{
    public $pic; // 随机图片地址或者图片地址
    public $str; // 显示文字 布尔型 默认不显示
    public $coordinate; //显示坐标 布尔型 默认不显示
    public $pics; //图片流
    /**
     * 错误信息
     * @var string
     */
    private $error = '';

    /**
     * 当前完整文件名 图片流
     * @var string
     */
    protected $filename;

    /**
     * 上传文件名
     * @var string
     */
    protected $saveName;
    /**
     * 上传文件命名规则
     * @var string
     */
    protected $rule = 'date';
    protected $tot; //旋转角度
    /**
     * 上传文件验证规则
     * @var array
     */
    protected $validate = [];

    /**
     * 是否单元测试
     * @var bool
     */
    protected $isTest;

    /**
     * 上传文件信息
     * @var array
     */
    protected $info = [];

    /**
     * 文件hash规则
     * @var array
     */
    protected $hash = [];

    /**
     * 架构函数
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     */
    public function __construct($pic = 'https://api.uomg.com/api/rand.img1', $str = false, $coordinate = false, $tot = 0)
    {

        $this->pic = $pic;
        $this->str = $str;
        $this->tot = $tot;
        $this->coordinate = $coordinate;
    }

    /**
     * @param  string $imgpath 要处理的图片路径
     * @return 图片数据
     */
    public function changeCircularImg()
    {
        $this->pics = imagecreatefromjpeg($this->pic);
        //$tot = rand(20, 300);
        //$src_img = null;
        //$src_img =$this->filename;
        $w = imagesx($this->pics);
        $h = imagesy($this->pics);
        $img = imagecreatetruecolor($w, $h);
        $src_img = $this->pic($w, $h);
        return $src_img;

    }
    /**
     * 保存文件
     * @access public
     * @param  string           $path    保存路径
     * @param  string|bool      $savename    保存的文件名 默认自动生成
     * @param  boolean          $replace 同名文件是否覆盖
     * @param  bool             $autoAppendExt     自动补充扩展名
     * @return false|File       false-失败 否则返回File实例
     */
    public function move($path, $savename = true, $replace = true, $autoAppendExt = true)
    {
        $path = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        // 文件保存命名规则
        $saveName = $this->buildSaveName($savename, $autoAppendExt);
        $filename = $path . $saveName;

        // 检测目录
        if (false === $this->checkPath(dirname($filename))) {
            return false;
        }

        /* 移动文件 */
        if (!imagepng($this->changeCircularImg($this->$tot), $filename)) {
            $this->error = 'upload write error';
            return false;
        }

        // 返回 File对象实例
        // $file = new self($filename);
        //$file->setSaveName($saveName);
        //$file->setUploadInfo($this->info);

        return $saveName;
    }
    public function getname()
    {
        return $this->buildSaveName(true);
    }
    /**
     * 获取保存文件名
     * @access protected
     * @param  string|bool   $savename    保存的文件名 默认自动生成
     * @param  bool          $autoAppendExt     自动补充扩展名
     * @return string
     */
    protected function buildSaveName($savename, $autoAppendExt = true)
    {
        if (true === $savename) {
            // 自动生成文件名
            $savename = $this->autoBuildName();
        }
        if ($autoAppendExt && false === strpos($savename, '.')) {
            $savename .= '.png';
        }

        return $savename;
    }

    /**
     * 自动生成文件名
     * @access protected
     * @return string
     */
    protected function autoBuildName()
    {
        if ($this->rule instanceof \Closure) {
            $savename = call_user_func_array($this->rule, [$this]);
        } else {
            switch ($this->rule) {
                case 'date':
                    $savename = date('Ymd') . DIRECTORY_SEPARATOR . md5(microtime(true));
                    break;
                default:
                    if (in_array($this->rule, hash_algos())) {
                        $hash = $this->hash($this->rule);
                        $savename = substr($hash, 0, 2) . DIRECTORY_SEPARATOR . substr($hash, 2);
                    } elseif (is_callable($this->rule)) {
                        $savename = call_user_func($this->rule);
                    } else {
                        $savename = date('Ymd') . DIRECTORY_SEPARATOR . md5(microtime(true));
                    }
            }
        }

        return $savename;
    }
    /**
     * 获取文件名
     * @access public
     * @param  string $type
     * @return string
     */
    public function hash()
    {
        $this->hash[$type] = md5(rand() . uniqid() . time());

        return $this->hash[$type];
    }
    /**
     * 检查目录是否可写
     * @access protected
     * @param  string   $path    目录
     * @return boolean
     */
    protected function checkPath($path)
    {
        if (is_dir($path)) {
            return true;
        }

        if (mkdir($path, 0755, true)) {
            return true;
        }

        $this->error = ['directory {:path} creation failed', ['path' => $path]];
        return false;
    }
/**
 * 检查目录是否可写
 * @access protected
 * @param  string   $tot    旋转角度
 * @return boolean
 */
    private function pic($src_w, $src_h)
    {
        // list($src_w, $src_h) = getimagesize($src_img); // 获取原图尺寸
        $dst_w = 350;
        $dst_h = 350;
        $dst_scale = $dst_h / $dst_w; //目标图像长宽比
        $src_scale = $src_h / $src_w; // 原图长宽比

        if ($src_scale >= $dst_scale) { // 过高
            $w = intval($src_w);
            $h = $w;

            $x = 0;
            $y = ($src_h - $h) / 2;
        } else { // 过宽
            $h = intval($src_h);
            $w = $h;

            $x = ($src_w - $w) / 2;
            $y = 0;
        }

        // 剪裁
        // $source = imagecreatefromjpeg($src_img);
        $croped = imagecreatetruecolor($w, $h);
        imagecopy($croped, $this->pics, 0, 0, $x, $y, $src_w, $src_h);
        //$img1 = imagerotate($croped, 77, imagecolorallocatealpha($croped, 0, 0, 0, 127));
        //这一句一定要有
        imagesavealpha($croped, true);
        //拾取一个完全透明的颜色,最后一个参数127为全透明
        $bg = imagecolorallocatealpha($croped, 0, 0, 0, 127);
        imagefill($croped, 0, 0, $bg);
        $r = $w / 2; //圆半径
        $y_x = $r; //圆心X坐标
        $y_y = $r; //圆心Y坐标
        $w = imagesx($croped);
        $h = imagesy($croped);

        $img = imagecreatetruecolor($w, $h);
        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $rgbColor = imagecolorat($croped, $x, $y);
                if (((($x - $r) * ($x - $r) + ($y - $r) * ($y - $r)) < ($r * $r))) {
                    imagesetpixel($img, $x, $y, $rgbColor);
                }
            }
        }

        $img1 = imagerotate($img,  $this->tot, imagecolorallocatealpha($img, 0, 0, 0, 127));
        $w1 = imagesx($img1);
        $h1 = imagesy($img1);
        $x = intval(($w1 - $w) / 2);
        $y = intval(($h1 - $h) / 2);
        imagecopy($img, $img1, 0, 0, $x, $y, $w1, $h1);
        // 缩放
        $scale = $dst_w / $w;
        $target = imagecreatetruecolor($dst_w, $dst_h);
        $final_w = intval($w * $scale);
        $final_h = intval($h * $scale);
        imagecopyresampled($target, $img, 0, 0, 0, 0, $final_w, $final_h, $w, $h);
        if ($this->str) {
            $black = imagecolorallocate($target, 88, 170, 104); //设置一个颜色变量为黑色
            $ttfPath = __DIR__ . '/2.ttf';
            $str = $this->getRandomString(1);
            imagettftext($target, 160,  $this->tot, 140, 190, $black, $ttfPath, $str);
        }
        if ($this->coordinate) {
            $black1 = imagecolorallocate($target, 167, 100, 174); //设置一个颜色变量为黑色
            $ttfPath1 = __DIR__ . '/2.ttf';
            imagettftext($target, 30, 0, 100, 100, $black1, $ttfPath1, $this->tot);
        }
        imagedestroy($croped);
        imagedestroy($img);
        imagedestroy($img1);
        imagedestroy($this->pics);
        return $target;
    }

    /**
     * 随机字母数字
     */
    private function getRandomString($len, $chars = null)
    {
        if (is_null($chars)) {
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        }
        mt_srand(10000000 * (double) microtime());
        for ($i = 0, $str = '', $lc = strlen($chars) - 1; $i < $len; $i++) {
            $str .= $chars[mt_rand(0, $lc)];
        }
        return $str;
    }
}
