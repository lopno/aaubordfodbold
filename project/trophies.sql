
--
-- When the trophies become available, these should be inserted
--

-- Get the best goals scored- ratio
-- "Best Score Ratio"
SELECT ID
FROM
(
    SELECT winnerID as ID, winScore as scoreFor, lossScore as scoreAgainst
    FROM `matches`
    WHERE NOT team
    UNION
    SELECT loserID as ID, lossScore as scoreFor, winScore as scoreAgainst
    FROM `matches`
    WHERE NOT team
) AS goals
    JOIN players ON (ID = playerID)
GROUP BY ID
ORDER BY SUM(scoreFor)/SUM(scoreAgainst) DESC
limit 1
-- Extra
SELECT SUM(scoreFor)/SUM(scoreAgainst)
FROM
(
    SELECT winnerID as ID, winScore as scoreFor, lossScore as scoreAgainst
    FROM `matches`
    WHERE NOT team
    UNION
    SELECT loserID as ID, lossScore as scoreFor, winScore as scoreAgainst
    FROM `matches`
    WHERE NOT team
) AS goals
    JOIN players ON (ID = playerID)
GROUP BY ID
ORDER BY SUM(scoreFor)/SUM(scoreAgainst) DESC
limit 1


-- "Point Grabber"
SELECT winnerID
FROM `matches`
WHERE NOT team
ORDER BY points DESC, timeCreated ASC
limit 1
-- Extra
SELECT points
FROM `matches`
WHERE NOT team
ORDER BY points DESC, timeCreated ASC
limit 1

-- "Dominator"
SELECT winnerID
FROM `matches`
WHERE NOT team and lossScore = 0
GROUP BY winnerID
ORDER BY COUNT(*) DESC, MAX(timeCreated) ASC
limit 1
-- Extra
SELECT count(*)
FROM `matches`
WHERE NOT team and lossScore = 0
GROUP BY winnerID
ORDER BY COUNT(*) DESC, MAX(timeCreated) ASC
limit 1