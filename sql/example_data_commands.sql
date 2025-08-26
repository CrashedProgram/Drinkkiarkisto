-- Lisää käyttäjiä
INSERT INTO users (username, password_hash, email, user_type) VALUES
('Matti87', '$2y$10$FuKy1PGGa6K8TrfVT8LdHO8LKhLOK9dgY.EfnJELqwHRkTFHEy2rS', 'matti@example.fi', 1),
('Liisa_K', '$2y$10$H9JIQeJG1pGUdxs5whgOBeDJR7INaO02FVK0MfMXY5vl5Kt/a35z.', 'liisa@example.fi', 1),
('AdminAntti', '$2y$10$DvXsqMAqTMVj4BgMOKyDGeP3dXOMk7x0hWQ4gYbBJZd4ehk4WXm0W', 'antti@example.fi', 2),
('JuomaJussi', '$2y$10$A0Dl5QDnE1nsaPXaK.Mz5ut.XjwVA.LZZ8mWqosJNrFndD1e4DFSC', 'jussi@example.fi', 1),
('BaarimikkoPekka', '$2y$10$pGsZj5ExKqUypWZG5vpAHO1.Th0yXH3/iXdnrMKpkQyYM0lG1Hx0m', 'pekka@example.fi', 1);

-- Lisää kategorioita
INSERT INTO categories (name) VALUES
('Klassikot'),
('Kesäiset'),
('Trooppiset'),
('Alkoholittomat'),
('Kuumat juomat'),
('Shotit');

-- Lisää ainesosia
INSERT INTO ingredients (name) VALUES
('Vodka'),
('Koskenkorva'),
('Jaloviina'),
('Gin'),
('Valkoviini'),
('Punaviini'),
('Kuohuviini'),
('Appelsiinimehu'),
('Ananasmehu'),
('Karpalomehu'),
('Sitruunamehu'),
('Limemehu'),
('Jääpalat'),
('Mansikka'),
('Mustikka'),
('Sokeri'),
('Kahvi'),
('Kaakao'),
('Kerma'),
('Kookosmaito'),
('Tonic'),
('Sitruuna'),
('Lime'),
('Mintunlehti'),
('Kanelitanko');

-- Lisää juomia
INSERT INTO drinks (name, category_id, author_id, is_approved, has_allergens, recipe_notes) VALUES
('Lonkero', 2, 1, TRUE, FALSE, 'Suomalainen klassikko. Sekoita ainekset ja tarjoile jäiden kanssa.'),
('Mansikkamargarita', 3, 2, TRUE, TRUE, 'Makeahko trooppinen juoma. Koristele mansikkaviipaleella.'),
('Jäätee', 4, 3, TRUE, FALSE, 'Raikas kesäjuoma. Valmista tee etukäteen ja anna jäähtyä jääkaapissa.'),
('Glögi', 5, 4, TRUE, TRUE, 'Perinteinen joulujuoma. Lämmitä hitaasti, älä keitä.'),
('Kossushotti', 6, 1, TRUE, FALSE, 'Tarjoile jääkylmänä.'),
('Minttukaakao', 5, 2, FALSE, TRUE, 'Täydellinen talvi-iltaan. Lisää kermavaahto ja suklaalastu päälle.'),
('Mustikkasmoothie', 4, 3, TRUE, TRUE, 'Terveellinen vaihtoehto. Voidaan tarjota aamupalana.'),
('Gin & Tonic', 1, 5, TRUE, FALSE, 'Klassinen gin-juoma. Koristele limellä tai kurkkuviipaleella.');

-- Lisää reseptin ainesosia
INSERT INTO recipe_ingredients (drink_id, ingredient_id, amount, unit) VALUES
-- Lonkero
(1, 4, 4, 'cl'),
(1, 21, 12, 'cl'),
(1, 13, 5, 'kpl'),

-- Mansikkamargarita
(2, 1, 4, 'cl'),
(2, 14, 6, 'kpl'),
(2, 12, 2, 'cl'),
(2, 16, 1, 'tl'),
(2, 13, 6, 'kpl'),

-- Jäätee
(3, 17, 20, 'cl'),
(3, 16, 2, 'tl'),
(3, 22, 1, 'viipale'),
(3, 24, 3, 'kpl'),

-- Glögi
(4, 6, 50, 'cl'),
(4, 16, 3, 'rkl'),
(4, 25, 1, 'kpl'),

-- Kossushotti
(5, 2, 4, 'cl'),

-- Minttukaakao
(6, 18, 20, 'cl'),
(6, 24, 5, 'kpl'),
(6, 19, 2, 'rkl'),

-- Mustikkasmoothie
(7, 15, 1, 'dl'),
(7, 20, 2, 'dl'),
(7, 16, 1, 'tl'),

-- Gin & Tonic
(8, 4, 5, 'cl'),
(8, 21, 15, 'cl'),
(8, 23, 1, 'viipale'),
(8, 13, 4, 'kpl');

-- Lisää juomien tilastot
INSERT INTO drink_statistics (drink_id, likes_count, dislikes_count) VALUES
(1, 25, 3),
(2, 18, 4),
(3, 12, 1),
(4, 30, 5),
(5, 42, 8),
(6, 7, 1),
(7, 15, 2),
(8, 38, 3);

-- Lisää ääniä
INSERT INTO drink_votes (user_id, drink_id, is_like) VALUES
(1, 2, TRUE),
(1, 4, TRUE),
(1, 5, FALSE),
(2, 1, TRUE),
(2, 3, TRUE),
(2, 8, TRUE),
(3, 5, TRUE),
(3, 6, TRUE),
(4, 1, TRUE),
(4, 2, FALSE),
(4, 7, TRUE),
(5, 3, FALSE),
(5, 5, TRUE),
(5, 8, TRUE);
