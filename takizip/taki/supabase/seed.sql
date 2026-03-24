INSERT INTO courses (title, category, description, progress_default)
VALUES
    ('Developpement Java Avance', 'Developpement', 'Collections, streams et POO avancee.', 75),
    ('UI/UX Design avec Tailwind', 'Design', 'Creation d interfaces modernes et responsives.', 30),
    ('Methodologies Agile & Scrum', 'Business', 'Organisation de projet en equipe agile.', 10)
ON CONFLICT (title)
DO UPDATE SET
    category = EXCLUDED.category,
    description = EXCLUDED.description,
    progress_default = EXCLUDED.progress_default;
