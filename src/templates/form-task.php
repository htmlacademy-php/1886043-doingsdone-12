<div class="content">
    <section class="content__side">
        <h2 class="content__side-heading">Проекты</h2>

        <nav class="main-navigation">
        <ul class="main-navigation__list">
            <?php foreach ($projects as $project) : ?>
                <li class="main-navigation__list-item <?= ($project['id'] === $projectId) ? 'main-navigation__list-item--active' : '' ?>">
                    <a class="main-navigation__list-item-link" href="<?= getProjectUrl($project['id']) ?>"><?= htmlspecialchars($project['title']); ?></a>
                    <span class="main-navigation__list-item-count"><?= ($project['count']) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>

        <a class="button button--transparent button--plus content__side-button" href="/src/add-project.php">Добавить проект</a>
    </section>

    <main class="content__main">
        <h2 class="content__main-heading">Добавление задачи</h2>

        <form class="form" action="add.php" method="post" autocomplete="off" enctype="multipart/form-data">
            <div class="form__row">
                <?php $classname = isset($errors['name']) ? "form__input--error" : ""; ?>
                <label class="form__label" for="name">Название <sup>*</sup></label>
                <input class="form__input <?=$classname;?>" type="text" name="name" id="name" value="<?=getPostVal('name'); ?>" placeholder="Введите название">
                <p class = "form__message"><span class="error_text"><?=$errors['name'] ?? ""; ?></span></p>
            </div>

            <div class="form__row">
                <?php $classname = isset($errors['project']) ? "form__input--error" : ""; ?>
                <label class="form__label" for="project">Проект <sup>*</sup></label>

                <select class="form__input form__input--select <?=$classname;?>" name="project" id="project">
                    <option value=""> </option>
                    <?php foreach ($projects as $project) : ?>
                        <option value="<?php echo $project['id'] ?>"><?php echo $project['title'] ?></option>
                    <?php endforeach; ?>
                </select>

                <p class = "form__message"><span class="error_text"><?=$errors['project'] ?? ""; ?></span></p>
            </div>

            <div class="form__row">
                <?php $classname = isset($errors['date']) ? "form__input--error" : ""; ?>
                <label class="form__label" for="date">Дата выполнения</label>

                <input class="form__input form__input--date" type="text" name="date" id="date" value="<?=getPostVal('date'); ?>" placeholder="Введите дату в формате ГГГГ-ММ-ДД">
                <p class = "form__message"><span class="error_text"><?=$errors['date'] ?? ""; ?></span></p>
            </div>

            <div class="form__row">
                <label class="form__label" for="file">Файл</label>

                <div class="form__input-file">
                    <input class="visually-hidden" type="file" name="task" id="task" value="">

                    <label class="button button--transparent" for="task">
                        <span>Выберите файл</span>
                    </label>
                </div>
            </div>

            <div class="form__row form__row--controls">
                <input class="button" type="submit" name="submit" value="Добавить">
            </div>
        </form>
    </main>
</div>
