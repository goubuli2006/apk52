<?php

use CodeIgniter\Pager\PagerRenderer;

/**
 * @var PagerRenderer $pager
 */
$pager->setSurroundCount(3);

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

<?php if ($pager->hasPrevious()) : ?>
        <a href="<?= str_replace("?page=","", $pager->getFirst()).'/';  ?>" aria-label="First">First</a>
        <a class="first" href="<?= str_replace("?page=","", $preLink).'/';  ?>" aria-label="Previous">Previous</a>
<?php endif ?>
<?php if ($pager->getPageCount() > 1) :?>
<?php foreach ($pager->links() as $link) : ?>
<?php if ($link['active']) {?>
        <a class="current"><?= $link['title'] ?></a>
<?php } else { ?>
        <span><a href="<?= str_replace("?page=","",$link['uri']).'/'; ?>" > <?= $link['title'] ?></a></span>
<?php } ?>
<?php endforeach ?>
<?php endif ?>
<?php if ($pager->hasNext()) : ?>
        <a href="<?= str_replace("?page=","", $nextLink).'/'; ?>" aria-label="Next">Next</a>
        <a href="<?= str_replace("?page=","", $pager->getLast()).'/';  ?>" aria-label="Last">Last</a>
<?php endif ?>
