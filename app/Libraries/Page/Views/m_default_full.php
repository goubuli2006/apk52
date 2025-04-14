<?php

use CodeIgniter\Pager\PagerRenderer;

/**
 * @var PagerRenderer $pager
 */
$pager->setSurroundCount(1);

$nextLink = '';
$preLink = '';
$linkData = [];
if ($pager->links()) {
    $linkData = $pager->links();

    foreach ($linkData as $key=> &$link) {
        if (!empty($link['uri'])){
            $link['uri'] = str_replace("index.php/","",$link['uri']);
        }

        if ($link['active']){
            if (isset($linkData[$key+1]) && !empty($linkData[$key+1])){
                $nextLink  = $linkData[$key+1]['uri'];
            }
            if (isset($linkData[$key-1]) && !empty($linkData[$key-1])){
                $preLink  = $linkData[$key-1]['uri'];
            }
        }
    }
}

?>

<?php if($pager->getPageCount() >1){?>
    <a href="<?= str_replace("?page=","", $pager->getFirst()).'/';  ?>" aria-label="First">First</a>
    <?php if($pager->getCurrentPageNumber() == 1){?>
        <a class="disabled" href="javascript:;" aria-label="Previous"><i class="icon left"></i></a>
    <?php }else{ ?>
        <a  href="<?= str_replace("?page=","", $preLink).'/';  ?>" aria-label="Previous Page"><i class="icon left"></i></a>
    <?php }?>
    
    <?php if ($pager->hasPrevious()) : ?>
    <?php endif ?>

    <?php foreach ($pager->links() as $link) : ?>
        <a href="<?= str_replace("?page=","",$link['uri']).'/'; ?>" <?= $link['active'] ? 'class="current"' : '' ?>><?= $link['title'] ?></a>
    <?php endforeach ?>

    <?php if ($pager->hasNext()) : ?>
    <?php endif ?>

    <?php if($pager->getCurrentPageNumber() == $pager->getPageCount()){?>
        <a class="disabled" href="javascript:;" aria-label="Next"><i class="icon right"></i> </a>
    <?php }else{ ?>
        <a href="<?= str_replace("?page=","", $nextLink).'/'; ?>" aria-label="Next"><i class="icon right"></i></a>
    <?php }?>
    <a href="<?= str_replace("?page=","", $pager->getLast()).'/';  ?>" aria-label="Last">Last </a>
<?php }?>