-- TRIGGERS FOR NOTIFICATIONS

DROP TRIGGER IF EXISTS post_notification_created;
DELIMITER $$
CREATE TRIGGER post_notification_created
AFTER INSERT ON posts
FOR EACH ROW
BEGIN
  INSERT INTO notifications (user_id, type)
  VALUES (NEW.user_id, 'post_created');
END $$
DELIMITER ;

DROP TRIGGER IF EXISTS post_notification_modded;
DELIMITER $$
CREATE TRIGGER post_notification_modded
AFTER UPDATE ON posts
FOR EACH ROW
BEGIN
  DECLARE done BOOLEAN DEFAULT FALSE;
  DECLARE post_creator_follower INT;
  DECLARE followers_cursor CURSOR FOR
    SELECT follower_id FROM followers WHERE user_id = NEW.user_id;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

  CASE
    WHEN NEW.permission = '3' THEN
      OPEN followers_cursor;

      followers_cursor_loop: LOOP
        FETCH followers_cursor INTO post_creator_follower;
        
        IF done THEN
          LEAVE followers_cursor_loop;
        END IF;
        
          INSERT INTO notifications (user_id, type, type_id, causer_id)
          VALUES (post_creator_follower, 'post', NEW.id, NEW.user_id);
      END LOOP followers_cursor_loop;

      CLOSE followers_cursor;

      INSERT INTO notifications (user_id, type, type_id)
      VALUES (NEW.user_id, 'post_approved', NEW.id);

    WHEN NEW.permission = '2' THEN
      INSERT INTO notifications (user_id, type)
      VALUES (NEW.user_id, 'post_rejected');
      
  END CASE;
END $$
DELIMITER ;

DROP TRIGGER IF EXISTS follow_notification;
DELIMITER $$
CREATE TRIGGER follow_notification
AFTER INSERT ON followers
FOR EACH ROW
BEGIN
  INSERT INTO notifications (user_id, type, type_id, causer_id)
  VALUES (NEW.user_id, 'follow', NEW.follower_id, NEW.follower_id);
END $$
DELIMITER ;

DROP TRIGGER IF EXISTS comments_notification;
DELIMITER $$
CREATE TRIGGER comments_notification
AFTER INSERT ON comments
FOR EACH ROW
BEGIN
  DECLARE parent_comment_user_id INT;
  DECLARE post_user_id INT;

  IF NEW.parent_comment_id IS NOT NULL THEN
    SELECT user_id INTO parent_comment_user_id FROM comments WHERE id = NEW.parent_comment_id;

    INSERT INTO notifications (user_id, type, type_id, causer_id)
    VALUES (parent_comment_user_id, 'reply', NEW.post_id, NEW.user_id);
  ELSE
    SELECT user_id INTO post_user_id FROM posts WHERE id = NEW.post_id;

    INSERT INTO notifications (user_id, type, type_id, causer_id)
    VALUES (post_user_id, 'comment', NEW.post_id, NEW.user_id);
  END IF;
END $$
DELIMITER ;


DROP TRIGGER IF EXISTS like_notification;
DELIMITER $$
CREATE TRIGGER like_notification
AFTER INSERT ON post_reactions
FOR EACH ROW
BEGIN
  DECLARE post_user_id INT;

  SELECT user_id INTO post_user_id FROM posts WHERE id = NEW.post_id;

  INSERT INTO notifications (user_id, type, type_id, causer_id)
  VALUES (post_user_id, 'like', NEW.post_id, NEW.user_id);
END $$

-- DUE TO THE LIMITATION OF THE TABLE, I WILL NOT BE ABLE TO CREATE THE REST OF THE TRIGGERS
-- THE REST OF THE TRIGGERS WILL BE CREATED AFTER REJECTION FEATURES HAVE BEEN IMPLEMENTED
