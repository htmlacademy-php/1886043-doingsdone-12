<div class="content">
    <section class="content__side">
        <h2 class="content__side-heading">Проекты</h2>

        <nav class="main-navigation">
        <ul class="main-navigation__list">
            <?php foreach ($projects as $project) : ?>
                <li class="main-navigation__list-item <?= ($project['id'] == $projectId) ? 'main-navigation__list-item--active' : '' ?>">
                    <a class="main-navigation__list-item-link" href="<?= getProjectUrl($project['id']) ?>"><?= htmlspecialchars($project['title']); ?></a>
                    <span class="main-navigation__list-item-count"><?= ($project['count']) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>

        <a class="button button--transparent button--plus content__side-button" href="add-project.php">Добавить проект</a>
    </section>

    <main class="content__main">
      <h2 class="content__main-heading">Добавление проекта</h2>

      <form class="form"  action="add-project.php" method="post" autocomplete="off">

        <div class="form__row">
                <?php $classname = isset($errors['project_name']) ? "form__input--error" : ""; ?>
                <label class="form__label" for="project_name">Название <sup>*</sup></label>
                <input class="form__input <?=$classname;?>" type="text" name="project_name" id="project_name" value="<?=getPostVal('name'); ?>" placeholder="Введите название проекта">
                <p class = "form__message"><span class="error_text"><?=$errors['project_name'] ?? ""; ?></span></p>
        </div>

        <div class="form__row form__row--controls">
          <input class="button" type="submit" name="submit" value="Добавить">
        </div>
      </form>
    </main>
  </div>
