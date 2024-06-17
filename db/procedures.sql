-- PROCEDURES IN MYSQL
DROP PROCEDURE IF EXISTS save_image;
DELIMITER $$
CREATE PROCEDURE save_image(
  p_table TEXT,
  p_id INT,
  p_image TEXT
)
BEGIN
  CASE p_table
    WHEN 'post' THEN
      INSERT INTO images (post_id, image) VALUES (p_id, p_image);
    WHEN 'comment' THEN
      INSERT INTO images (comment_id, image) VALUES (p_id, p_image);
    WHEN 'dm' THEN
      INSERT INTO images (dms_id, image) VALUES (p_id, p_image);
    ELSE
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid table';
  END CASE;
END
$$
DELIMITER ;
-- call as: CALL save_image('post', 1, 'image');

DROP PROCEDURE IF EXISTS delete_image;
DELIMITER $$
CREATE PROCEDURE delete_image(
  p_id INT
)
BEGIN
  DELETE FROM images
  WHERE images.id = p_id;
END
$$
DELIMITER ;
-- call as: CALL delete_image(1);

