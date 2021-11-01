<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Validator;

class mod_common extends Model
{
    //响应码
    const SUCCESS = 0;
    const FAIL = -1;
    const ERROR = -1;
    //上传目录数量
    public static $dir_num = 128;
    //允许文件格式
    public static $allowed_types = 'jpeg|jpg|gif|png|bmp|webp|mp4|zip|rar|gz|bz2|xls|xlsx|pdf|doc|docx';
    // 允许上传文件大小的最大值（单位 KB），设置为 0 表示无限制
    public static $max_size = 5*1024;
    //默认时区
    public static $timezone_set    = 'Asia/Taipei';
    //默认需要转化的时区
    public static $to_timezone    = 'ETC/GMT-7';

    //分頁器
    protected function pages($total, $page_size = 10, $page_no = null, $page_name = 'page')
    {
        //防止$page_size字段为空字符串而报错
        $page_size = !empty($page_size) ? $page_size : 10;
        $pages = new LengthAwarePaginator([], $total, $page_size, $page_no, [
            //分頁地址
            'path' => Paginator::resolveCurrentPath(),
            //第幾頁參數命名
            'pageName' => $page_name,
        ]);

        return $pages;
    }

    //寫入日誌封裝
    public static function logger($name, $data)
    {
        //項目名
        $app_name = config('app.name');

        $data_str = $data;
        if(is_array($data) || is_object($data))
        {
            $data_str = json_encode($data, JSON_UNESCAPED_UNICODE);
        }

        //有狀態錯誤則記錄到錯誤日誌
        if (isset($data['status']) && $data['status'] <= 0)
        {
            log::error("{$app_name}->{$name}->{$data_str}\n\n");
        }
        //普通日誌
        else
        {
            log::info("{$app_name}->{$name}->{$data_str}\n\n");
        }

        return true;
    }



    /**
     * 匯出列表數據
     * @param array $args
     */
    public static function export_data(array $args)
    {
        try
        {
            //要匯出數據
            $rows  = isset($args['rows']) ? $args['rows']:[];
            //匯出類型
            $format  = isset($args['format']) ? $args['format']:'csv';
            //列表字段
            $fields  = isset($args['fields']) ? $args['fields']:[];
            //数组不怕填多一些字段，有用到的会用来显示，没用到的填了也不会影响程序,所以尽管把多个tab列表页用到的字段都填进去吧
            $titles  = isset($args['titles']) ? $args['titles']:[];
            //指定导出文件路径
            $file  = isset($args['file']) ? $args['file']:'';
            //当前页数
            $page_no  = isset($args['page_no']) ? $args['page_no']:null;
            //总页数
            $total_page  = isset($args['total_page']) ? $args['total_page']:null;

            if(!is_array($fields) || empty(array_filter($fields))) //過濾空数组
            {
                return mod_common::error('未选择任何字段', -1);
            }

            if($rows)
            {
                $export_rows = [];
                foreach ($rows as $_item) //需要导出数据
                {
                    $_new_item = [];
                    foreach ($fields as $field)
                    {
                        $field_val = isset($_item[$field]) ? strip_tags($_item[$field]) : '-';

                        $_new_item[] = $field_val;
                    }
                    $export_rows[] = $_new_item;
                }

                if ($page_no == 1)
                {
                    //生成的文件名
                    $excel_file = 'excel_'.mod_display::datetime(time(), null, 'YmdHis').'.'.$format;
                }
                else
                {
                    $excel_file = $file;
                }

                if (empty($excel_file))
                {
                    return mod_common::error('参数错误[file为空]', -2);
                }

                $file_path = storage_path('app/public/excel')."/".$excel_file;
                $fp = fopen($file_path, 'a+'); //a+讀寫模式開啟
                if ($fp === false)
                {
                    return mod_common::error('导出失败，请稍后重试', -3);
                }

                if ($page_no == 1)
                {
                    $export_titles = [];//导出文件字段
                    foreach ($fields as $_field)
                    {
                        //干掉字符串中的 HTML、XML、PHP标签
                        $export_titles[] = isset($titles[$_field]) ? strip_tags($titles[$_field]) : '-';
                    }
                    fwrite($fp, "\xEF\xBB\xBF"); //防止亂碼
                    fputcsv($fp, $export_titles); //格式化為csv文件,先寫入欄目
                }

                foreach($export_rows as $v)
                {
                    fputcsv($fp, $v); //寫入數據
                }
                fclose($fp); //最後記得關閉該文件

                return mod_common::success([
                    'file'       => $excel_file,
                    'excel_file' => mod_common::get_server_file_url($excel_file, 'excel'),
                    'total_page' => $total_page,
                ]);
            }
        }
        catch (\Exception $e)
        {
            $status = $e->getCode();
            //記錄日誌
            mod_common::logger(__METHOD__, [
                'status'  => $status,
                'errcode' => $e->getCode(),
                'errmsg'  => $e->getMessage(),
            ]);
        }
    }

    /**
     * 用户密码加密接口,默认使用算法bcrypt,长度默认60字元
     * @param $password
     * @return string
     */
    public static function password_hash($password)
    {
        //return bcrypt($password);
        return app('hash')->make($password);
    }

    /**
     * 检测密码
     * @param $password         明文
     * @param $hash_password    密文
     * @return mixed
     */
    public static function check_password($password, $hash_password)
    {
        return Hash::check($password, $hash_password);
    }

    /**
     * 生成唯一识别
     * @param string $type  類型
     * @param int $length   字元长度
     * @return string
     */
    public static function random($type = 'web', $length = 32)
    {
        switch($type)
        {
            case 'basic':
                return mt_rand();   //使用 Mersenne Twister 算法返回随机整数
                break;
            case 'alnum':
            case 'numeric':
            case 'nozero':
            case 'alpha':
            case 'distinct':
            case 'hexdec':
                switch ($type)
                {
                    case 'alpha':
                        $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        break;

                    default:
                    case 'alnum':
                        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        break;

                    case 'numeric':
                        $pool = '0123456789';
                        break;

                    case 'nozero':
                        $pool = '123456789';
                        break;

                    case 'distinct':
                        $pool = '2345679ACDEFHJKLMNPRSTUVWXYZ';
                        break;

                    case 'hexdec':
                        $pool = '0123456789abcdef';
                        break;
                }

                $str = '';
                for ($i=0; $i < $length; $i++)
                {
                    $str .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
                }
                return $str;
                break;
            case 'sha1' :
                return sha1(uniqid(mt_rand(), true));
                break;
            case 'uuid':
                $pool = ['8', '9', 'a', 'b'];
                return sprintf('%s-%s-4%s-%s%s-%s',
                    static::random('hexdec', 8),
                    static::random('hexdec', 4),
                    static::random('hexdec', 3),
                    $pool[array_rand($pool)],
                    static::random('hexdec', 3),
                    static::random('hexdec', 12));
                break;
            case 'unique':
                //会产生大量的重复数据
                //$str = uniqid();
                //生成的唯一标识中没有重复
                //版本>=7.1,使用 session_create_id()
                $str = version_compare(PHP_VERSION,'7.1.0','ge') ? md5(session_create_id()) : md5(uniqid(md5(microtime(true)),true));
                if ( $length == 32 )
                {
                    return $str;
                }
                else
                {
                    return substr($str, 8, 16);
                }
                break;
            case 'web':
                // 即使同一个IP，同一款浏览器，要在微妙内生成一样的随机数，也是不可能的
                // 进程ID保证了并发，微妙保证了一个进程每次生成都会不同，IP跟AGENT保证了一个网段
                // md5(当前进程id在目前微秒时间生成唯一id + 当前ip + 当前浏览器)
                $remote_addr = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'; //兼容cli本地调用时会报错
                $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? ''; //兼容cli本地调用时会报错
                $str = md5(getmypid().uniqid(md5(microtime(true)),true).$remote_addr.$user_agent);
                if ( $length == 32 )
                {
                    return $str;
                }
                else
                {
                    return substr($str, 8, 16);
                }
                break;
            default:
        }
    }

    /**
     * 生成token, 32位
     * @return string
     */
    public static function make_token()
    {
        return self::random('web');
    }

    /**
     * 输出JSON
     * @param array $array
     * @return \Illuminate\Http\JsonResponse
     */
    public static function exit_json(array $array)
    {
        //api访问日志
        if(defined('IN_API')) //api端才寫入日志
        {
            mod_api_req_log::add_log(['res_data' => $array]);
        }

        //return response()->json($array);
        header('Content-type: application/json'); //定義嚮應頭部
        print json_encode($array);
        exit();
    }

    /**
     * API成功响应
     * @param array $data
     * @param string $msg
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success($data = [], $msg = 'success')
    {
        $array = [
            'code'      => self::SUCCESS,
            'msg'       => (string)$msg,
            'timestamp' => time(),
            'data'      => $data
        ];

        return self::exit_json($array);
    }

    /**
     * API失败响应
     * @param string $msg
     * @param int $code
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function error($msg = 'error', $code = self::FAIL, $data = [])
    {
        $array = [
            'code'      => (int)$code,
            'msg'       => (string)$msg,
            'timestamp' => time(),
            'data'      => $data
        ];
        return self::exit_json($array);
    }

    /**
     * 參數錯誤
     * @return \Illuminate\Http\JsonResponse
     */
    public static function invalid_params()
    {
        return self::error(trans('api.api_param_error'), self::ERROR);
    }

    /**
     * 服务异常
     * @param string $msg
     * @return \Illuminate\Http\JsonResponse
     */
    public static function unknown_error($msg = '服务异常，请稍后重试')
    {
        //記錄日誌
        mod_common::logger(__METHOD__, [
            'status'    => self::ERROR,
            'errcode'   => self::ERROR,
            'errmsg'    => $msg,
            'req_ac'    => request()->route()->getActionName(),
            'req_data'  => request()->all(), //送的全部參數
        ]);

        return self::error(trans('api.api_server_error'), self::ERROR);
    }

    /**
     * 无权限
     * @return \Illuminate\Http\JsonResponse
     */
    public static function no_permission()
    {
        return self::error(trans('api.api_no_permission'), self::ERROR);
    }

    /**
     * 抛出异常
     * @param $code
     * @param $msg
     * @throws \Exception
     */
    public static function abort($code, $msg)
    {
        throw new \Exception($msg, $code);
    }

    /**
     * 自定义数组转字符串
     *
     * @param array $arr
     * @return string
     */
    public static function array_to_str($arr = [])
    {
        if(empty($arr))
        {
            return '';
        }
        return json_encode($arr, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 自定义字符串转数组
     *
     * @param string $str
     * @return mixed|string
     */
    public static function str_to_array($str = '')
    {
        if(empty($str))
        {
            return '';
        }

        return json_decode($str, true);
    }

    /**
     * 获取随机伪造IP
     *
     * @return string
     */
    public static function get_random_client_ip()
    {
        $ip = rand(0, 255).'.'.rand(0, 255).'.'.rand(0, 255).'.'.rand(0, 255);
        return $ip;
    }

    /**
     * 将远程图片下载至本地
     *
     * @param string $img_url
     * @return string
     */
    public static function get_remote_image($img_url = '', $dir = '')
    {
        $filename = self::get_remote_image_name($img_url, $dir);

        // 如果需要分隔目录上传
        $upload_dir = storage_path('app/public/');
        $upload_dir = empty($dir) ? $upload_dir : "{$upload_dir}{$dir}/";

        if(file_exists($upload_dir.$filename))
        {
            return $filename;
        }

        //如果没有http或者https则补默认http前缀
        if(strpos($img_url,'//') === 0)
        {
            $img_url = 'http:'.$img_url;
        }

        //防止空格下载不了
        $img_url = str_replace(' ', '%20', $img_url);

        $imgdata = @file_get_contents($img_url);//獲取數據流

        if($imgdata === false)
        {
            return $img_url;
        }

        file_put_contents($upload_dir.$filename, $imgdata);

        return $filename;
    }

    /**
     * 获取上传远程图片名称
     *
     * @param string $img_url
     * @param string $dir
     * @param string $content_type 文件類型
     * @return string
     */
    public static function get_remote_image_name($img_url = '', $dir = '', $content_type = '')
    {
        //如果不是URL的图片略过
        if(strpos($img_url,'//') === false)
        {
            return $img_url;
        }

        //如果没有http或https则补默认http前缀
        if(strpos($img_url,'//') === 0)
        {
            $img_url = 'http:'.$img_url;
        }

        //防止空格下载不了
        $img_url = str_replace(' ', '%20', $img_url);

        $image_suffix = mod_common::get_image_suffix($img_url);

        $filename = @md5_file($img_url);
        $filename = empty($filename) ? mod_common::random('web') : $filename;
        $filename = $filename.$image_suffix;

        //如果需要分隔目录上传
        $upload_dir = storage_path('app/public/');
        $upload_dir = empty($dir) ? $upload_dir : "{$upload_dir}{$dir}/";

        if (self::$dir_num > 0)
        {
            $dir_num = self::str2number($filename, self::$dir_num);
            //檢測目錄是否存在,不存在則創建
            mod_common::path_exists($upload_dir.'/'.$dir_num);
            $filename = $dir_num.'/'.$filename;
        }

        return empty($dir) ? $filename : "{$dir}/{$filename}";
    }

    // 字符串转数字，用于分表和图片分目录
    public static function str2number($str, $maxnum = 128)
    {
        // 位数
        $bitnum = 1;
        if ($maxnum >= 100)
        {
            $bitnum = 3;
        }
        elseif ($maxnum >= 10)
        {
            $bitnum = 2;
        }

        // sha1:返回一个40字符长度的16进制数字
        $str = sha1(strtolower($str));
        // base_convert:进制建转换，下面是把16进制转成10进制，方便做除法运算
        // str_pad:把字符串填充为指定的长度，下面是在左边加0，共 $bitnum 位
        $str = str_pad(base_convert(substr($str, -2), 16, 10) % $maxnum, $bitnum, "0", STR_PAD_LEFT);
        return $str;
    }

    /**
     * 检查路径是否存在
     * @param $path
     * @return bool
     */
    public static function path_exists($path)
    {
        $pathinfo = pathinfo($path . '/tmp.txt');

        if ( !empty( $pathinfo ['dirname'] ) )
        {
            if (file_exists ( $pathinfo ['dirname'] ) === false)
            {
                if (@mkdir ( $pathinfo ['dirname'], 0777, true ) === false)
                {
                    return false;
                }
            }
        }
        return $path;
    }

    /**
     * 获取图片后缀
     *
     * @param string $image_url
     * @param string $content_type 文件類型
     * @return bool|string
     */
    public static function get_image_suffix($image_url = '', $content_type = '')
    {
        if(empty($image_url))
        {
            return false;
        }

        //$image_url = empty($content_type) ? self::get_remote_file_content_type($image_url) : $image_url;

        if(strpos($image_url, 'png') !== false)
        {
            $image_suffix = '.png';
        }
        elseif (strpos($image_url, 'jpg') !== false)
        {
            $image_suffix = '.jpg';
        }
        elseif (strpos($image_url, 'gif') !== false)
        {
            $image_suffix = '.gif';
        }
        elseif (strpos($image_url, 'jpeg') !== false)
        {
            $image_suffix = '.jpg';
        }
        elseif(strpos($image_url,'.bmp') !== false)
        {
            $image_suffix = '.bmp';
        }
        else
        {
            return false;
        }

        return $image_suffix;
    }

    /**
     * 普通上传
     *
     * @param string $formname
     * @param string $dir
     * @param int $thumb_width
     * @param float $thumb_height
     * @return array
     */
    public static function upload(Request $request, $formname = 'file', $dir = 'image', $thumb_w = 0, $thumb_h = 0)
    {
        // 判断是否存在上传的文件
        if ($request->hasFile($formname))
        {
            $file = $request->file($formname);
            $upload_dir = storage_path('app/public/');
            $upload_dir = empty($dir) ? $upload_dir : "{$upload_dir}{$dir}";

            // 目录不存在则生成
            if (!mod_common::path_exists($upload_dir))
            {
                mod_common::abort(-1, '保存目录不存在');
            }

            $filesize = $file->getSize(); //原文件大小

            $realname = $file->getClientOriginalName(); //原文件名 testimg.jpg
            $file_ext = $file->getClientOriginalExtension();  //扩展名 jpg
            //$tmp_name  = $file->getFilename(); //临时文件名 php1Z8ML9
            $tmp_name  = $file->getRealPath(); //临时文件名 /Applications/MAMP/tmp/php/php1Z8ML9
            log::debug("上传开始：{$realname}");//记录日志

            $allowed_types = explode('|', self::$allowed_types);
            if (!in_array($file_ext, $allowed_types))
            {
                mod_common::abort(-2, '上传的文件格式不符合规定');
            }

            // 判断文件大小
            if (self::$max_size != 0)
            {
                $max_size = self::$max_size * 1024;
                if ($filesize > $max_size)
                {
                    mod_common::abort(-3, '上传的文件太大');
                }
            }

            //md5_file要给绝对定址，否则会报错
            $filename = md5_file($tmp_name).'.'.$file_ext;

            // 如果需要分隔目录上传
            if (self::$dir_num > 0)
            {
                $dir_num = mod_common::str2number($filename, self::$dir_num);
                mod_common::path_exists($upload_dir.'/'.$dir_num);
                $filename = $dir_num.'/'.$filename;
            }

            $dir_num = empty($dir_num) ? '':$dir_num;
            //將文件從暫存位置（由PHP設定來決定）移動至你指定的永久保存位置
            if ($file->move($upload_dir.'/'.$dir_num, $filename))
            {
                @chmod($upload_dir.'/'.$filename, 0777);

                $filelink = Storage::disk('public')->url("{$dir}/{$filename}");

                if ($thumb_w > 0 || $thumb_h > 0)
                {
                    list($filename, $filelink) = self::thumb($upload_dir, $filename, $file_ext, $thumb_w, $thumb_h);
                }

                $ret = [
                    'realname' => $realname,
                    'filename' => $filename,
                    'filelink' => $filelink,
                    'src' => $filelink, //因应layedit图片上传接口增加src字段
                ];

                log::debug("上传成功：{$realname}->{$ret['filename']}");//记录日志
            }
        }

        return empty($ret) ? []:$ret;
    }

    /**
     * 缩图
     * @param $upload_dir
     * @param $filename
     * @param string $file_ext
     * @param int $thumb_w
     * @param int $thumb_h
     * @return array
     */
    public static function thumb( $upload_dir, $filename, $file_ext = 'jpg', $thumb_w = 0, $thumb_h = 0 )
    {
        $pathinfo = getimagesize($upload_dir.'/'.$filename);
        $width  = $pathinfo[0]; //上傳圖片原始寬
        $height = $pathinfo[1]; //上傳圖片原始高

        // 缩略图的临时目录
        $filepath_tmp = storage_path('app/public').'/tmp';
        // 缩略图的临时文件名
        $filename_tmp = md5($filename).'.'.$file_ext;

        // 目录不存在则生成
        if (!mod_common::path_exists($filepath_tmp))
        {
            mod_common::abort(-4, '保存目录不存在');
        }

        $img = Image::make($upload_dir.'/'.$filename);

        if ( $thumb_w > 0 && $thumb_h > 0 )
        {
            $img->resize($thumb_w, $thumb_h)->save($filepath_tmp.'/'.$filename_tmp);
        }
        // 只设置了宽度，自动计算高度，高度等比例缩放
        elseif ( $thumb_w > 0 && $thumb_h == 0 )
        {
            $img->widen($thumb_w)->save($filepath_tmp.'/'.$filename_tmp);
        }
        // 只设置了高度，自动计算宽度，宽度等比例缩放
        elseif ( $thumb_h > 0 && $thumb_w == 0 )
        {
            $img->heighten($thumb_h)->save($filepath_tmp.'/'.$filename_tmp);
        }

        $filename = md5_file($filepath_tmp.'/'.$filename_tmp).".".$file_ext;
        //$filename = uniqid().'.'.$file_ext;

        // 如果需要分隔目录上传
        if (self::$dir_num > 0)
        {
            $dir_num = mod_common::str2number($filename, self::$dir_num);
            if (!mod_common::path_exists($upload_dir.'/'.$dir_num))
            {
                mod_common::abort(-5, '保存目录不存在');
            }
            $filename = $dir_num.'/'.$filename;
        }

        //不同路徑的話，移動檔案並更名
        rename($filepath_tmp.'/'.$filename_tmp, "{$upload_dir}/{$filename}");

        $filelink = Storage::disk('public')->url("image/{$filename}");
        return [$filename, $filelink];
    }


    //获取服务器文件链接
    public static function get_server_file_url($file, $dir = '')
    {
        if (empty($file))
        {
            return '';
        }
        return Storage::disk('public')->url("{$dir}/{$file}");
    }

    //获取管理后台时区
    public static function get_admin_timezone()
    {
        return self::$to_timezone; //例：金邊所在時區 ETC/GMT-7
    }

    /**
     * 格式化时间输出
     *
     * @param $timestamp
     * @param $timezone
     * @param string $format
     * @return string
     */
    public static function format_date($timestamp, $timezone, $format='Y/m/d H:i')
    {
        if (empty($timestamp))
        {
            return '';
        }

        //检查时区是否合法，防止时区乱写报错，不合法使用默认时区
        try
        {
            new \DateTimeZone($timezone);
        }
        catch(\Exception $e)
        {
            $timezone = self::get_admin_timezone();
        }
        return self::time_convert(['datetime' => $timestamp, 'to_timezone' => $timezone, 'format' => $format]);
    }

    /**
     * 不同时区时间转换
     * @param  array  $data
     * mod_common::time_convert([
     *      'datetime'      => KALI_TIMESTAMP,//可以是时间格式或者时间戳
     *      'from_timezone' => 'ETC/GMT-7',//默认为系统设置的时区，即 ETC/GMT
     *      'to_timezone'   => 'ETC/GMT-8',//转换成为的时区，默认获取用户所在国家对应时区
     *      'format'        => ''//格式化输出字符串。默认为Y-m-d H:i:s
     * ]);
     *
     * 一般直接使用 mod_common::time_convert(['datetime' => xxxxx]);
     * @return string
     */
    public static function time_convert($data = array())
    {
        $datetime      = empty($data['datetime']) ? time() : $data['datetime'];
        $datetime      = is_numeric($datetime) ? '@'.$datetime : $datetime;
        $from_timezone = empty($data['from_timezone']) ? self::$timezone_set : $data['from_timezone'];
        //需要转化的时区
        $to_timezone   = empty($data['to_timezone']) ? self::$to_timezone : $data['to_timezone'];
        $format        = empty($data['format']) ? 'Y-m-d H:i:s' : $data['format'];

        $date_obj = new \DateTime($datetime, new \DateTimeZone($from_timezone));
        $date_obj->setTimezone(new \DateTimeZone($to_timezone));
        return $date_obj->format($format);
    }

    //时间转时间戳
    public static function date_convert_timestamp($date, $timezone)
    {
        //数字直接返回
        if (preg_match("/^\d*$/", $date))
        {
            return $date;
        }

        if(empty($timezone) || !is_string($timezone))
        {
            return strtotime($date);
        }

        //非法日期
        if (!strtotime($date))
        {
            return 0;
        }

        //时区不合法使用默认时区
        try
        {
            $timezone = new \DateTimeZone($timezone);
        }
        catch (\Exception $e)
        {
            $timezone = new \DateTimeZone(self::get_admin_timezone());
        }

        $date_obj = new \DateTime($date, $timezone);
        $time = $date_obj->format('U');

        return $time;
    }

    /**
     * 检查签名
     *
     * @param array $data
     * @param $app_key
     * @param $check_sign 客户端送过来的签名
     * @return bool
     */
    public static function check_sign(array $data, $app_key, $check_sign)
    {
        $_sign = self::sign($data, $app_key);
        return $_sign === $check_sign;
    }

    /**
     * 签名方法
     *
     * @param array $data
     * @param $app_key 私鑰
     * @param array $exclude 不参加签名参数
     * @return string
     */
    public static function sign(array $data, $app_key, $exclude = ['sign'])
    {
        //干掉sign参数
        if (!empty($exclude) && is_array($exclude))
        {
            foreach ($exclude as $key)
            {
                unset($data[$key]);
            }
        }

        ksort($data); //依键名做正序

        $query_str = http_build_query($data); //转成 a=xxx&b=xxx
        $query_arr = explode('&', $query_str);
        //由于http_build_query会对参数进行一次urlencode，所以这里需要加多一层urldecode
        $query_arr = array_map(function ($item) {
            return urldecode($item); //例：%E6%9D%8E%E8%81%B0%E6%98%8E => 李聰明
        }, $query_arr);

        $sign_text = implode('&', $query_arr);
        $sign_text .= '&key=' . $app_key;
        return strtoupper(md5($sign_text)); //md5不支持解密回原来字符串
    }

    /**
     * 该函数用于过滤，对用户提交的数据进行处理
     *
     * @param $filter
     * @param $data
     * @param bool $magic_slashes
     */
    public static function data_filter($filter, $data, $msg = [], $magic_slashes = true)
    {
        $ret = [];
        $msg = empty($msg) ? ['required' => ':attribute']:$msg; //只返回错误字段名
        $validator = Validator::make($data, $filter, $msg);

        if($validator->fails())
        {
            //返回第一个字段错误信息
            $ret = current($validator->errors()->all());
        }
        else
        {
            $ret = $data;
        }

        return $ret;
    }

    /**
     * 將HTML幾個特殊字元跳脫成HTML Entity(格式：&xxxx;)格式
     * 包括(&),('),("),(<),(>)五個字符
     * @param $data
     * @return array|string
     */
    public static function htmlentities($data)
    {
        if (is_array($data))
        {
            foreach ($data as $k => $v)
            {
                $data[$k] = self::htmlentities($data[$k]);
            }
        }
        else
        {
            //同时转义双,单引号
            $data = htmlspecialchars(trim($data), ENT_QUOTES);
        }

        return $data;
    }

    /**
     * 获取当前地址的Action
     * @return mixed
     */
    public static function get_action()
    {
        $uses = isset(request()->route()[1]["uses"]) ? request()->route()[1]["uses"] : '';
        return  explode('@', $uses)[1];
    }

    /**
     * 获取当前地址的Action
     * @return mixed
     */
    public static function get_controller()
    {
        $uses = isset(request()->route()[1]["uses"]) ? request()->route()[1]["uses"] : '';
        return explode('@', $uses)[0];
    }
}
