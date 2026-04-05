INSERT INTO courses (title, category, description, progress_default)
VALUES
    ('Developpement Java Avance', 'Developpement', 'Collections, streams et POO avancee.', 75),
    ('UI/UX Design avec Tailwind', 'Design', 'Creation d interfaces modernes et responsives.', 30),
    ('Methodologies Agile & Scrum', 'Business', 'Organisation de projet en equipe agile.', 10)
ON DUPLICATE KEY UPDATE
    category = VALUES(category),
    description = VALUES(description),
    progress_default = VALUES(progress_default);
