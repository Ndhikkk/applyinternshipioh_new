<?php

use CodeIgniter\Pager\PagerRenderer;

/**
 * @var PagerRenderer $pager
 */
$pager->setSurroundCount(2);
?>

<nav aria-label="<?= lang('Pager.pageNavigation') ?>">
    <ul class="pagination mb-0">
        <?php if ($pager->hasPrevious()) : ?>
            <li class="page-item">
                <a class="page-link page-nav-btn" href="<?= $pager->getFirst() ?>" aria-label="Halaman pertama" title="Halaman pertama">
                    <i class="bi bi-chevron-bar-left"></i>
                    <span class="page-nav-label">First</span>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link page-nav-btn" href="<?= $pager->getPrevious() ?>" aria-label="Halaman sebelumnya" title="Sebelumnya">
                    <i class="bi bi-chevron-left"></i>
                    <span class="page-nav-label">Previous</span>
                </a>
            </li>
        <?php else : ?>
            <li class="page-item disabled">
                <span class="page-link page-nav-btn" aria-hidden="true">
                    <i class="bi bi-chevron-bar-left"></i>
                    <span class="page-nav-label">First</span>
                </span>
            </li>
            <li class="page-item disabled">
                <span class="page-link page-nav-btn" aria-hidden="true">
                    <i class="bi bi-chevron-left"></i>
                    <span class="page-nav-label">Previous</span>
                </span>
            </li>
        <?php endif ?>

        <li class="page-item page-separator" aria-hidden="true">
            <span class="page-link page-divider">│</span>
        </li>

        <?php foreach ($pager->links() as $link) : ?>
            <li class="page-item page-number <?= $link['active'] ? 'active' : '' ?>">
                <?php if ($link['active']) : ?>
                    <span class="page-link"><?= $link['title'] ?></span>
                <?php else : ?>
                    <a class="page-link" href="<?= $link['uri'] ?>"><?= $link['title'] ?></a>
                <?php endif ?>
            </li>
        <?php endforeach ?>

        <li class="page-item page-separator" aria-hidden="true">
            <span class="page-link page-divider">│</span>
        </li>

        <?php if ($pager->hasNext()) : ?>
            <li class="page-item">
                <a class="page-link page-nav-btn" href="<?= $pager->getNext() ?>" aria-label="Halaman berikutnya" title="Berikutnya">
                    <span class="page-nav-label">Next</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link page-nav-btn" href="<?= $pager->getLast() ?>" aria-label="Halaman terakhir" title="Halaman terakhir">
                    <span class="page-nav-label">Last</span>
                    <i class="bi bi-chevron-bar-right"></i>
                </a>
            </li>
        <?php else : ?>
            <li class="page-item disabled">
                <span class="page-link page-nav-btn" aria-hidden="true">
                    <span class="page-nav-label">Next</span>
                    <i class="bi bi-chevron-right"></i>
                </span>
            </li>
            <li class="page-item disabled">
                <span class="page-link page-nav-btn" aria-hidden="true">
                    <span class="page-nav-label">Last</span>
                    <i class="bi bi-chevron-bar-right"></i>
                </span>
            </li>
        <?php endif ?>
    </ul>
</nav>
