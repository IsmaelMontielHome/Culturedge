-- PROCEDURES IN MYSQL FOR POSTS
DROP PROCEDURE IF EXISTS save_post;
DELIMITER $$
CREATE PROCEDURE save_post(
  p_user_id INT,
  p_title TEXT,
  p_description TEXT,
  p_theme_id INT,
  OUT inserted_id INT
)
BEGIN
  INSERT INTO posts (title, description, user_id, theme)
  VALUES (p_title, p_description, p_user_id, p_theme_id);
  SET inserted_id = LAST_INSERT_ID();
END $$
DELIMITER ;
-- call as: CALL save_post('title', 'description');

DROP PROCEDURE IF EXISTS update_post;
DELIMITER $$
CREATE PROCEDURE update_post(
  p_id INT,
  p_title TEXT,
  p_description TEXT,
  p_theme_id INT
)
BEGIN
  UPDATE posts
  SET title = p_title, description = p_description, permission = "1", theme = p_theme_id
  WHERE posts.id = p_id;
END $$
DELIMITER ;

-- call as: CALL update_post(1, 'title', 'description');

DROP PROCEDURE IF EXISTS delete_post;
DELIMITER $$

CREATE PROCEDURE delete_post(
  p_id INT
)
BEGIN
  
  SET FOREIGN_KEY_CHECKS = 0;
  
  DELETE FROM comments
  WHERE post_id = p_id;
  
  DELETE FROM posts
  WHERE id = p_id;
  
  SET FOREIGN_KEY_CHECKS = 1;
END $$
DELIMITER ;

-- call as: CALL delete_post(1);

DROP PROCEDURE IF EXISTS get_all_posts;
DELIMITER $$

CREATE PROCEDURE get_all_posts()
BEGIN
  SELECT 
    posts.*,
    users.username,
    users.email,
    COALESCE(COUNT(DISTINCT pr.post_id), 0) AS total_reactions,
    COALESCE(COUNT(DISTINCT c.id), 0) AS total_comments
  FROM posts
  INNER JOIN users ON posts.user_id = users.id
  LEFT JOIN post_reactions pr ON posts.id = pr.post_id
  LEFT JOIN comments c ON posts.id = c.post_id
  GROUP BY posts.id, users.username, users.email;
END
$$
DELIMITER ;


-- call as: CALL get_all_posts();

DROP PROCEDURE IF EXISTS get_post_by_id;
DELIMITER $$

CREATE PROCEDURE get_post_by_id(
  IN p_id INT
)
BEGIN
  SELECT 
    posts.*,
    users.username AS username,
    users.email AS email,
    COALESCE(COUNT(DISTINCT pr.post_id), 0) AS total_reactions,
    COALESCE(COUNT(DISTINCT c.id), 0) AS total_comments
  FROM posts
  INNER JOIN users ON posts.user_id = users.id
  LEFT JOIN post_reactions pr ON posts.id = pr.post_id
  LEFT JOIN comments c ON posts.id = c.post_id
  WHERE posts.id = p_id
  GROUP BY posts.id, users.username, users.email;
END
$$

DELIMITER ;

-- call as: CALL get_post_by_id(1);

DROP PROCEDURE IF EXISTS get_images_by_post_id;
DELIMITER $$
CREATE PROCEDURE get_images_by_post_id(
  p_id INT
)
BEGIN
  SELECT * FROM images
  WHERE images.post_id = p_id;
END
$$
DELIMITER ;
-- call as: CALL get_images_by_post_id(1);


