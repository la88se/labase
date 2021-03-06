<?php
define('_VALID', true);
require 'include/config.php';
require 'include/function_global.php';
require 'include/function_smarty.php';
require 'classes/pagination.class.php';

//
$sql_add	= NULL;
$sql_delim	= ' WHERE';
if ( $config['show_private_videos'] == '0' ) {
    $sql_add   .= $sql_delim. " type = 'public'";
    $sql_delim	= ' AND';
}

$sql_add       .= $sql_delim. " active = '1'";  

$sql            = "SELECT VID, title,is_vip, duration, addtime, thumb, thumbs, viewnumber, rate, likes, dislikes, type, hd,thumb_img
                   FROM video" .$sql_add. " ORDER BY viewtime DESC LIMIT " .$config['watched_per_page'];
$rs             = $conn->execute($sql);
$viewed_videos  = $rs->getrows();

$viewed_total   = count($viewed_videos);


/*最新视频分页功能*/
$sql            = "SELECT count(VID) AS total_videos FROM video" .$sql_add;
$rsc            = $conn->execute($sql);
$total          = $rsc->fields['total_videos'];
$pagination     = new Pagination($config['recent_per_page']);
$limit          = $pagination->getLimit($total);
$sql            = "SELECT VID, title, duration, addtime, thumb, thumbs, viewnumber, rate, likes, dislikes, type, hd,thumb_img
                   FROM video" .$sql_add. " ORDER BY addtime DESC LIMIT " .$limit;
$rs             = $conn->execute($sql);
$recent_videos  = $rs->getrows();

// $sql            = "SELECT VID, title, duration, addtime, thumb, thumbs, viewnumber, rate, likes, dislikes, type, hd,thumb_img
//                    FROM video" .$sql_add. " ORDER BY addtime DESC LIMIT " .$config['recent_per_page'];
// $rs             = $conn->execute($sql);
// $recent_videos  = $rs->getrows();


$page_link      = $pagination->getPagination('/');
$smarty->assign('page_link', $page_link);


$smarty->assign('pageHome','on');

$categories     = get_categories();
$smarty->assign('category',0);
$smarty->assign('categories',$categories);

$smarty->assign('errors',$errors);
$smarty->assign('messages',$messages);
$smarty->assign('menu', 'home');
$smarty->assign('index', true);
$smarty->assign('viewed_total', $viewed_total);
$smarty->assign('viewed_videos', $viewed_videos);
$smarty->assign('recent_videos', $recent_videos);
$smarty->assign('self_title', $seo['index_title']);
$smarty->assign('self_description', $seo['index_desc']);
$smarty->assign('self_keywords', $seo['index_keywords']);

if(is_mobile()){
    $smarty->display('header_m.tpl');
}else{
    $smarty->display('header.tpl');
}

$smarty->display('errors.tpl');
$smarty->display('messages.tpl');

if(is_mobile()){
    $smarty->display('index_m.tpl');
    $smarty->display('footer_m.tpl');
}else{
    $smarty->display('index.tpl');
    $smarty->display('footer.tpl');
}
$smarty->gzip_encode();
?>
