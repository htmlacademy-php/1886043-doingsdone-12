USE `doingsdone` ;

-- -----------------------------------------------------
-- INSERT Table `user` придумайте пару пользователей;
-- -----------------------------------------------------
INSERT INTO users (id, registration_date, email, name, password) VALUE ('1', '17.12.2021', 'nikolay@mail.com', 'Николай', '12345');
INSERT INTO users (id, registration_date, email, name, password) VALUE ('2', '18.12.2021', 'konstya@mail.com', 'Константин', '67890');

-- -----------------------------------------------------
-- INSERT Table `projects` существующий список проектов;
-- -----------------------------------------------------
INSERT INTO projects (id, name, user_id) VALUE ('1', 'Входящие', '1');
INSERT INTO projects (id, name, user_id) VALUE ('2', 'Учеба', '1');
INSERT INTO projects (id, name, user_id) VALUE ('3', 'Работа', '1');
INSERT INTO projects (id, name, user_id) VALUE ('4', 'Домашние дела', '1');
INSERT INTO projects (id, name, user_id) VALUE ('5', 'Авто', '1');
INSERT INTO projects (id, name, user_id) VALUE ('21', 'Входящие', '2');
INSERT INTO projects (id, name, user_id) VALUE ('22', 'Учеба', '2');
INSERT INTO projects (id, name, user_id) VALUE ('23', 'Работа', '2');
INSERT INTO projects (id, name, user_id) VALUE ('24', 'Домашние дела', '2');
INSERT INTO projects (id, name, user_id) VALUE ('25', 'Авто', '2');

-- -----------------------------------------------------
-- INSERT Table `task` существующий список задач;
-- -----------------------------------------------------
INSERT INTO tasks (name, deadline, project_id, is_finished) VALUE ('Собеседование в IT компании', '01.07.2022', '3');
INSERT INTO tasks (name, deadline, project_id, is_finished) VALUE ('Выполнить тестовое задание', '25.01.2022', '3');
INSERT INTO tasks (name, deadline, project_id, is_finished) VALUE ('Сделать задание первого раздела', '31.12.2021', '2', TRUE);
INSERT INTO tasks (name, deadline, project_id, is_finished) VALUE ('Встреча с другом', '30.12.2021', '1');
INSERT INTO tasks (name, deadline, project_id, is_finished) VALUE ('Купить корм для кота', NULL, '4');
INSERT INTO tasks (name, deadline, project_id, is_finished) VALUE ('Заказать пиццу', NULL, '4');
INSERT INTO tasks (name, deadline, project_id, is_finished) VALUE ('Собеседование в IT компании', '01.07.2022', '23');
INSERT INTO tasks (name, deadline, project_id, is_finished) VALUE ('Выполнить тестовое задание', '25.01.2022', '23');
INSERT INTO tasks (name, deadline, project_id, is_finished) VALUE ('Сделать задание первого раздела', '31.12.2021', '22', TRUE);
INSERT INTO tasks (name, deadline, project_id, is_finished) VALUE ('Встреча с другом', '30.12.2021', '21');
INSERT INTO tasks (name, deadline, project_id, is_finished) VALUE ('Купить корм для кота', NULL, '24');
INSERT INTO tasks (name, deadline, project_id, is_finished) VALUE ('Заказать пиццу', NULL, '24');

-- -------------------------------------------------------------------------------
-- SELECT Table `project` получить список из всех проектов для одного пользователя;
-- -------------------------------------------------------------------------------
SELECT projects.name FROM projects JOIN users ON projects.user_id = users.id WHERE users.id = '1';

-- ---------------------------------------------------------------------
-- SELECT Table `task` получить список из всех задач для одного проекта;
-- ---------------------------------------------------------------------
SELECT tasks.name FROM tasks JOIN projects ON tasks.project_id = projects.id WHERE projects.id = '3';

-- -----------------------------------------------------
-- Table `task` пометить задачу как выполненную;
-- -----------------------------------------------------
UPDATE tasks SET is_finished = TRUE WHERE id = '4';

-- -----------------------------------------------------------
-- Table `task` обновить название задачи по её идентификатору.
-- -----------------------------------------------------------
UPDATE tasks SET name = 'Сделать второе задание первого раздела' WHERE id = '9';