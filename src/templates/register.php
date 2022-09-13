<div class="content">
        <section class="content__side">
          <p class="content__side-info">Если у вас уже есть аккаунт, авторизуйтесь на сайте</p>

          <a class="button button--transparent content__side-button" href="auth.php">Войти</a>
        </section>

        <main class="content__main">
          <h2 class="content__main-heading">Регистрация аккаунта</h2>

          <form class="form" action="register.php" method="post" autocomplete="off">

            <div class="form__row">
              <?php $classname = isset($errors['email']) ? "form__input--error" : ""; ?>
              <label class="form__label" for="email">E-mail <sup>*</sup></label>

              <input class="form__input <?=$classname;?>" type="text" name="email" id="email" value="<?=getPostVal('email'); ?>" placeholder="Введите e-mail">

              <p class = "form__message"><span class="error_text"><?=$errors['email'] ?? ""; ?></span></p>
            </div>

            <div class="form__row">
              <?php $classname = isset($errors['password']) ? "form__input--error" : ""; ?>
              <label class="form__label" for="password">Пароль <sup>*</sup></label>
              <input class="form__input <?=$classname;?>" type="password" name="password" id="password" value="<?=getPostVal('password'); ?>" placeholder="Введите пароль">
              <p class = "form__message"><span class="error_text"><?=$errors['password'] ?? ""; ?></span></p>
            </div>

            <div class="form__row">
              <?php $classname = isset($errors['name']) ? "form__input--error" : ""; ?>
              <label class="form__label" for="name">Имя <sup>*</sup></label>
              <input class="form__input <?=$classname;?>" type="text" name="name" id="name" value="<?=getPostVal('name'); ?>" placeholder="Введите имя">
              <p class = "form__message"><span class="error_text"><?=$errors['name'] ?? ""; ?></span></p>
            </div>

            <div class="form__row form__row--controls">
              <?php if (!empty($errors)) : ?>
                <p class="error-message">Пожалуйста, исправьте ошибки в форме</p>
              <?php else : ?>
                <p></p>
              <?php endif; ?>

              <input class="button" type="submit" name="submit" value="Зарегистрироваться">

            </div>
          </form>
        </main>
      </div>
