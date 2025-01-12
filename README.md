# u03-todo-WilliamLundquist

FIGMA MED ER DIAGRAM OCH SKISS:
https://www.figma.com/design/QIHyUjewqUo94rPcKFhdKM/Untitled?node-id=0-1&p=f&t=Rv5vZc3MNK4MRjXz-0

SQL KOD:

CREATE TABLE IF NOT EXISTS list (
    list_id int NOT NULL AUTO_INCREMENT,
    titel varchar(50),
    a_description varchar(255),
    is_done TINYINT(1) DEFAULT 0,
    is_list_favorite TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (list_id)
);

CREATE TABLE IF NOT EXISTS task (
    task_id int NOT NULL AUTO_INCREMENT,
    list_id int,
    titel varchar(50),
    a_description varchar(255),
    is_done TINYINT(1) DEFAULT 0,
    is_task_favorite TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(task_id),
    FOREIGN KEY(list_id) REFERENCES list (list_id) ON DELETE CASCADE
);

