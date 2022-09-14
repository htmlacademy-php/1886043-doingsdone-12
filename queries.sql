USE `doingsdone` ;

-- -----------------------------------------------------
-- INSERT Table `user` придумайте пару пользователей;
-- -----------------------------------------------------
INSERT INTO users (id, registration_date, email, name, password) VALUE ('1', '2021-12-17', 'nikolay@mail.com', 'Николай', '12345');
INSERT INTO users (id, registration_date, email, name, password) VALUE ('2', '2021-12-18', 'konstya@mail.com', 'Константин', '67890');
INSERT INTO users (id, registration_date, email, name, password) VALUE ('3', '2022-01-28', 'anastasia@mail.com', 'Анастасия', '13579');

-- -----------------------------------------------------
-- INSERT Table `projects` существующий список проектов;
-- -----------------------------------------------------
INSERT INTO projects (id, title, user_id) VALUE ('1', 'Входящие', '1');
INSERT INTO projects (id, title, user_id) VALUE ('2', 'Учеба', '1');
INSERT INTO projects (id, title, user_id) VALUE ('3', 'Работа', '1');
INSERT INTO projects (id, title, user_id) VALUE ('4', 'Домашние дела', '1');
INSERT INTO projects (id, title, user_id) VALUE ('5', 'Авто', '1');
INSERT INTO projects (id, title, user_id) VALUE ('21', 'Входящие', '2');
INSERT INTO projects (id, title, user_id) VALUE ('22', 'Учеба', '2');
INSERT INTO projects (id, title, user_id) VALUE ('23', 'Работа', '2');
INSERT INTO projects (id, title, user_id) VALUE ('24', 'Домашние дела', '2');
INSERT INTO projects (id, title, user_id) VALUE ('25', 'Авто', '2');
INSERT INTO projects (id, title, user_id) VALUE ('31', 'Маникюр', '3');
INSERT INTO projects (id, title, user_id) VALUE ('32', 'Шоппинг', '3');
INSERT INTO projects (id, title, user_id) VALUE ('33', 'Работа', '3');
INSERT INTO projects (id, title, user_id) VALUE ('34', 'Фитнес', '3');
INSERT INTO projects (id, title, user_id) VALUE ('35', 'Авто', '3');

-- -----------------------------------------------------
-- INSERT Table `task` существующий список задач;
-- -----------------------------------------------------
INSERT INTO tasks (name, deadline, project_id) VALUE ('Собеседование в IT компании', '2022-07-01', '33');
INSERT INTO tasks (name, deadline, project_id) VALUE ('Выполнить тестовое задание', '2022-01-25', '33');
INSERT INTO tasks (name, deadline, project_id, is_finished) VALUE ('Поменять цвет', '2021-12-31', '31', TRUE);
INSERT INTO tasks (name, deadline, project_id) VALUE ('Поменять цвет', '2021-12-30', '35');
INSERT INTO tasks (name, deadline, project_id) VALUE ('Купить корм для кота', NULL, '32');
INSERT INTO tasks (name, deadline, project_id) VALUE ('Заказать пиццу', NULL, '32');
INSERT INTO tasks (name, deadline, project_id) VALUE ('Собеседование в IT компании', '2022-07-01', '23');
INSERT INTO tasks (name, deadline, project_id) VALUE ('Выполнить тестовое задание', '2022-01-25', '23');
INSERT INTO tasks (name, deadline, project_id, is_finished) VALUE ('Сделать задание первого раздела', '2021-12-31', '22', TRUE);
INSERT INTO tasks (name, deadline, project_id) VALUE ('Встреча с другом', '2021-12-30', '21');
INSERT INTO tasks (name, deadline, project_id) VALUE ('Купить корм для кота', NULL, '24');
INSERT INTO tasks (name, deadline, project_id) VALUE ('Заказать пиццу', NULL, '24');

-- -------------------------------------------------------------------------------
-- SELECT Table `project` получить список из всех проектов для одного пользователя;
-- -------------------------------------------------------------------------------
SELECT projects.title FROM projects JOIN users ON projects.user_id = users.id WHERE users.id = '1';

-- ---------------------------------------------------------------------
-- SELECT Table `task` получить список из всех задач для одного проекта;
-- ---------------------------------------------------------------------
SELECT tasks.name FROM tasks JOIN projects ON projects.id = tasks.project_id WHERE projects.id = '3';

-- -----------------------------------------------------
-- Table `task` пометить задачу как выполненную;
-- -----------------------------------------------------
UPDATE tasks SET is_finished = TRUE WHERE id = '4';

-- -----------------------------------------------------------
-- Table `task` обновить название задачи по её идентификатору.
-- -----------------------------------------------------------
UPDATE tasks SET name = 'Сделать второе задание первого раздела' WHERE id = '9';