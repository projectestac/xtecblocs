<?php

function dateText($timestamp): string
{
    $monthName = [
        'de gener', 'de febrer', 'de mar&ccedil;', 'd\'abril', 'de maig', 'de juny', 'de juliol', 'd\'agost',
        'de setembre', 'd\'octubre', 'de novembre', 'de desembre',
    ];
    $dateText = 'el dia ' . date('d', $timestamp) . ' ' . $monthName[(int)date('n', $timestamp) - 1] . ' de ' .
        date('Y', $timestamp);

    // The timestamps of the posts are their local date read as UTC, so today is computed the same way
    $today = strtotime(current_time('Y-m-d'));

    $reldays = ($timestamp - $today) / 86400;

    if ($reldays >= 0 && $reldays < 1) {
        return 'avui';
    } elseif ($reldays >= -1 && $reldays < 0) {
        return 'ahir';
    }

    return $dateText;
}

function getNewsList(): array
{
    global $wpdb;
    $sql = "SELECT id,post_date,post_title FROM $wpdb->posts " .
        "WHERE `post_type`='post' and `post_status`='publish' ORDER BY ID DESC";
    $news = $wpdb->get_results($sql);
    $posts = [];

    foreach ($news as $new) {
        $posts[] = ['newId' => $new->id, 'new_title' => $new->post_title, 'post_date' => $new->post_date];
    }
    return $posts;
}


/**
 *
 * @access public
 * @author Greg 'Adam Baum'
 * @since 1.13 - 2002/01/23
 * @param integer $startnum start iteam
 * @param integer $total total number of items present
 * @param string $urltemplate template for url, will replace '%%' with item number
 * @param integer $perpage number of links to display (default=10)
 */
function Pager($startnum, $total, $urltemplate, $perpage = 20): ?string
{
    if (empty($startnum)) {
        $startnum = 1;
    }

    if (empty($perpage)) {
        $perpage = 10;
    }

    // Check that we are needed
    if ($total <= $perpage) {
        return null;
    }

    $sortida = '';

    // Show startnum link
    if ((int)$startnum !== 1) {
        $url = preg_replace('/%%/', 1, $urltemplate);
        $sortida .= 'P&agrave;gina <a href="' . $url . '"  style="text-decoration:none; color:#1E4588;"><<</a>';
    } else {
        $sortida .= 'P&agrave;gina <<';
    }

    $sortida .= ' ';

    $pagenum = 1;

    $sortida .= ' | ';

    for ($curnum = 1; $curnum <= $total; $curnum += $perpage) {
        if (($startnum < $curnum - 1) || ($startnum + 1 > ($curnum + $perpage - 1))) {
            if (
                (($pagenum % 10) === 0) // link if page is multiple of 10
                || ($pagenum === 1) // link first page
                || (($curnum > ($startnum - 4 * $perpage)) //link -3 and +3 pages
                && ($curnum < ($startnum + 4 * $perpage)))
            ) {
                $url = preg_replace('/%%/', $curnum, $urltemplate);
                $sortida .= '<a href="' . $url . '" style="text-decoration:none; color:#1E4588;">' . $pagenum . '</a>';
                $sortida .= ' | ';
            }
        } else {
            $sortida .= '<strong><u>' . $pagenum . '</u></strong>  | ';
        }
        $pagenum++;
    }
    if (($curnum >= $perpage + 1) && ($startnum < $curnum - $perpage)) {
        $url = preg_replace('/%%/', $curnum - $perpage, $urltemplate);
        $sortida .= '<a href="' . $url . '" style="text-decoration:none; color:#1E4588;">>></a>';
    } else {
        $sortida .= '>>';
    }
    return $sortida;
}
