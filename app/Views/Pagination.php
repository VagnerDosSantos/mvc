<?php
$url = $_SERVER['REQUEST_URI'];
$url = strtok($url, '?');

$isLastPage = ($pagination['currentPage'] == $pagination['lastPage']);
$isFirstPage = ($pagination['currentPage'] == 1);

$previousPage = '#';
$nextPage = '#';

if (!$isFirstPage) {
    $previousPage = $url . '?page=' . ($pagination['currentPage'] - 1);
}

if (!$isLastPage) {
    $nextPage = $url . '?page=' . ($pagination['currentPage'] + 1);
}

?>
<tr>
    <td colspan="6" class="right-align">
        <ul class="pagination">
            <li class="<?= $isFirstPage ? 'disabled' : '' ?>"><a href="<?= $previousPage ?>"><i class="material-icons">chevron_left</i></a></li>
            <?php for ($i = 0; $i < $pagination['lastPage']; $i++) : ?>
                <li class="waves-effect <?= ($pagination['currentPage'] == $i + 1) ? 'active-page' : '' ?>">
                    <a href="<?= $url ?>?page=<?= $i + 1 ?>"><?= $i + 1 ?></a>
                </li>
            <?php endfor ?>
            <li class="waves-effect <?= $isLastPage ? 'disabled' : '' ?>">

                <a href="<?= $nextPage ?>"><i class="material-icons">chevron_right</i></a>
            </li>
        </ul>
    </td>
</tr>