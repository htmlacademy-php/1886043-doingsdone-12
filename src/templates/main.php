<div class="content">
    <section class="content__side">
        <h2 class="content__side-heading">Проекты</h2>

        <nav class="main-navigation">
            <ul class="main-navigation__list">
                <?php foreach ($projects as $project) : ?>
                    <li class="main-navigation__list-item <?= ($project['id'] == $projectId) ? 'main-navigation__list-item--active' : '' ?>">
                        <a class="main-navigation__list-item-link" href="<?= getProjectUrl($project['id']) ?>"><?= htmlspecialchars($project['title']); ?></a>
                        <span class="main-navigation__list-item-count"><?= isset($project['count']) ? ($project['count']) : '0' ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <a class="button button--transparent button--plus content__side-button" href="pages/form-project.html" target="project_add">Добавить проект</a>

    </section>

    <main class="content__main">
        <h2 class="content__main-heading">Список задач</h2>

        <form class="search-form" action="index.php" method="post" autocomplete="off">
            <input class="search-form__input" type="text" name="" value="" placeholder="Поиск по задачам">

            <input class="search-form__submit" type="submit" name="" value="Искать">
        </form>

        <div class="tasks-controls">
            <nav class="tasks-switch">
                <a href="/" class="tasks-switch__item tasks-switch__item--active">Все задачи</a>
                <a href="/" class="tasks-switch__item">Повестка дня</a>
                <a href="/" class="tasks-switch__item">Завтра</a>
                <a href="/" class="tasks-switch__item">Просроченные</a>
            </nav>

            <label class="checkbox">
                <input class="checkbox__input visually-hidden show_completed" type="checkbox" <?= $showCompleteTasks === 1 ? "checked" : "" ?>>
                <span class="checkbox__text">Показывать выполненные</span>
            </label>
        </div>

        <table class="tasks">
            <?php foreach ($tasks as $currentTask) : ?>
                <?php if (($currentTask['is_finished']) && ($showCompleteTasks === 0)) : ?>
                    <?php continue ?>
                <?php else : ?>
                    <tr class="tasks__item task <?= ($currentTask['is_finished']) ? "task--completed" : less24hours($currentTask['deadline']) ?>">
                        <td class="task__select">
                            <label class="checkbox task__checkbox">
                                <input class="checkbox__input visually-hidden" type="checkbox" checked>

                                <span class="checkbox__text">
                                    <?php if ($currentTask['path_to_file']) : ?>
                                        <?php echo '<a href="', $currentTask['path_to_file'], '">'?>
                                    <?php endif ?>

                                    <?= htmlspecialchars($currentTask['name']) ?>

                                    <?php if ($currentTask['path_to_file']) : ?>
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
