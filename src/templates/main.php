<div class="content">
    <section class="content__side">
        <h2 class="content__side-heading">Проекты</h2>

        <nav class="main-navigation">
            <ul class="main-navigation__list">
                <?php foreach ($projects as $project) : ?>
                    <li class="main-navigation__list-item <?= ($project['id'] === $projectId) ? 'main-navigation__list-item--active' : '' ?>">
                        <a class="main-navigation__list-item-link" href="<?= getProjectUrl($project['id']) ?>"><?= htmlspecialchars($project['title']); ?></a>
                        <span class="main-navigation__list-item-count"><?= isset($project['count']) ? ($project['count']) : '0' ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <a class="button button--transparent button--plus content__side-button" href="/src/add-project.php" target="add_project">Добавить проект</a>

    </section>

    <main class="content__main">
        <h2 class="content__main-heading">Список задач</h2>

        <form class="search-form" action="index.php" method="get" autocomplete="off">
            <input class="search-form__input" type="text" name="searchTaskName" value="<?php (!empty($_GET['searchTaskName']))? print($_GET['searchTaskName']) : '' ?>" placeholder="Поиск по задачам">

            <input class="search-form__submit" type="submit" name="" value="Искать">
        </form>

        <div class="tasks-controls">
            <nav class="tasks-switch">
                <a href="<?= getDeadlineUrl('withoutTimeLimits', $showCompleteTasks) ?>" class="tasks-switch__item
                   <?= (isset($_GET['deadline'])&&(!$_GET['deadline']) !=='withoutTimeLimits') ? '' : 'tasks-switch__item--active' ?>
                   <?= (isset($_GET['deadline'])&&($_GET['deadline']) ==='withoutTimeLimits') ? 'tasks-switch__item--active' : '' ?>">
                   Все задачи
                </a>
                <a href="<?= getDeadlineUrl('today', $showCompleteTasks) ?>" class="tasks-switch__item <?= (isset($_GET['deadline'])&&($_GET['deadline']) ==='today') ? 'tasks-switch__item--active' : '' ?>">Повестка дня</a>
                <a href="<?= getDeadlineUrl('tomorrow', $showCompleteTasks) ?>" class="tasks-switch__item <?= (isset($_GET['deadline'])&&($_GET['deadline']) ==='tomorrow') ? 'tasks-switch__item--active' : '' ?>">Завтра</a>
                <a href="<?= getDeadlineUrl('yesterday', $showCompleteTasks) ?>" class="tasks-switch__item <?= (isset($_GET['deadline'])&&($_GET['deadline']) ==='yesterday') ? 'tasks-switch__item--active' : '' ?>">Просроченные</a>
            </nav>

            <label class="checkbox checkbox__comlete__tasks">
                <input class="checkbox__input visually-hidden show_completed" type="checkbox" <?= $showCompleteTasks === 1 ? "checked" : "" ?>>
                <span class="checkbox__text">Показывать выполненные</span>
            </label>
        </div>

        <table class="tasks">
            <?php $checked = 'checked'; ?>
            <?php foreach ($tasks as $currentTask) : ?>
                <?php if (($currentTask['is_finished']) && ($showCompleteTasks === 0)) : ?>
                    <?php continue ?>
                <?php else : ?>
                    <tr class="tasks__item task <?= ($currentTask['is_finished']) ? "task--completed" : less24hours($currentTask['deadline']) ?>">
                        <td class="task__select">
                            <label class="checkbox">

                                <input class="checkbox__input visually-hidden task__checkbox" type="checkbox" name="taskname" value="<?= ($currentTask['id']) ?>"
                                  <?= ($currentTask['is_finished'] === '1') ? $checked : "" ?>>

                                <span class="checkbox__text">

                                    <?= htmlspecialchars($currentTask['name']) ?>

                                    <?php if ($currentTask['path_to_file']) : ?>
                                      <?php echo '<a href="', $currentTask['path_to_file'], '">'?>
                                        <echo>&nbsp; &#128194;</echo>
                                      <?php echo '</a'?>
                                    <?php endif ?>
                                </span>

                            </label>
                        </td>
                        <td class="task__date"><?= htmlspecialchars($currentTask['deadline']); ?></td>
                        <td class="task__controls"> </td>
                    </tr>
                <?php endif ?>
            <?php endforeach; ?>
        </table>
    </main>
</div>
