DROP PROCEDURE IF EXISTS InsertUserReaction;
DELIMITER $$

CREATE PROCEDURE InsertUserReaction (
    IN p_userId INT,
    IN p_postId INT,
    IN p_reactType VARCHAR(255),
    OUT total_reactions INT
)
BEGIN
    INSERT INTO post_reactions (user_id, post_id, reaction_type) 
    VALUES (p_userId, p_postId, p_reactType);
    
    SELECT COUNT(*) INTO total_reactions 
    FROM post_reactions 
    WHERE post_id = p_postId;

END $$

DELIMITER ;

-- call as: CALL InsertUserReaction( );

DROP PROCEDURE IF EXISTS DeleteReaction;
DELIMITER $$

CREATE PROCEDURE DeleteReaction (
    IN p_userId INT,
    IN p_postId INT,
    OUT total_reactions INT
)
BEGIN

    DELETE FROM post_reactions
    WHERE user_id = p_userId AND post_id = p_postId;

    SELECT COUNT(*) INTO total_reactions
    FROM post_reactions
    WHERE post_id = p_postId;

END $$

DELIMITER ;

-- call as: CALL DeleteReaction( );


DROP PROCEDURE IF EXISTS getReactionForUser;

DELIMITER $$

CREATE PROCEDURE getReactionForUser(IN user_id INT, IN post_id INT)
BEGIN
    SELECT
        ur.reaction_type AS reactType
    FROM
        post_reactions ur
    WHERE
        ur.user_id = user_id
        AND ur.post_id = post_id;
END $$

DELIMITER ;

-- call as: CALL getReactionForUser( );

DROP PROCEDURE IF EXISTS GetUserReactions;

DELIMITER $$

CREATE PROCEDURE GetUserReactions(IN user_id INT)
BEGIN
    SELECT
        p.post_id,
        ur.reaction_type AS reactType
    FROM
        post_reactions ur
    JOIN (
        SELECT post_id
        FROM post_reactions
        GROUP BY post_id
    ) p ON ur.post_id = p.post_id
    WHERE
        ur.user_id = user_id;
END$$

DELIMITER ;

-- call as: CALL GetUserReactions( );
