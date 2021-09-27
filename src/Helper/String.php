<?php

/**
 * 人民币转换大写
 */
if (!function_exists('rmb_upper')) {
    function rmb_upper($num)
    {
        $num = round($num,2);  //取两位小数
        $num = ''.$num;  //转换成数字
        $arr = explode('.',$num);

        $str_left = $arr[0];
        $str_right = $arr[1] ?? 0;

        $len_left = strlen($str_left); //小数点左边的长度
        $len_right = strlen($str_right); //小数点右边的长度

        //循环将字符串转换成数组，
        for($i=0;$i<$len_left;$i++)
        {
            $arr_left[] = substr($str_left,$i,1);
        }

        for($i=0;$i<$len_right;$i++)
        {
            $arr_right[] = substr($str_right,$i,1);
        }

        //构造数组$daxie
        $daxie = array(
            '0'=>'零',
            '1'=>'壹',
            '2'=>'贰',
            '3'=>'叁',
            '4'=>'肆',
            '5'=>'伍',
            '6'=>'陆',
            '7'=>'柒',
            '8'=>'捌',
            '9'=>'玖',
        );

        //循环将数组$arr_left中的值替换成大写
        foreach($arr_left as $k => $v)
        {
            $arr_left[$k] = $daxie[$v];
            switch($len_left--)
            {
                //数值后面追加金额单位
                case 5:
                    $arr_left[$k] .= '万';break;
                case 4:
                    $arr_left[$k] .= '千';break;
                case 3:
                    $arr_left[$k] .= '百';break;
                case 2:
                    $arr_left[$k] .= '十';break;
                default:
                    $arr_left[$k] .= '元';break;
            }
        }

        foreach($arr_right as $k =>$v)
        {
            $arr_right[$k] = $daxie[$v];
            switch($len_right--)
            {
                case 2:
                    $arr_right[$k] .= '角';break;
                default:
                    $arr_right[$k] .= '分';break;
            }
        }

        //将数组转换成字符串，并拼接在一起
        $new_left_str = implode('',$arr_left);
        $new_right_str = implode('',$arr_right);

        $new_str = $new_left_str.$new_right_str;

        //如果金额中带有0，大写的字符串中将会带有'零千零百零十',这样的字符串，需要替换掉
        $new_str = str_replace('零万','零',$new_str);
        $new_str = str_replace('零千','零',$new_str);
        $new_str = str_replace('零百','零',$new_str);
        $new_str = str_replace('零十','零',$new_str);
        $new_str = str_replace('零零零','零',$new_str);
        $new_str = str_replace('零零','零',$new_str);
        $new_str = str_replace('零元','元',$new_str);
        if ($new_str == "元零分") {
            $new_str = '零元零分';
        }
        return $new_str;
    }
}