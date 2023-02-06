````SQL
UPDATE tableA a
INNER JOIN tableB b  ON b.a_id = a.id
SET a.v = b.v
WHERE a.v is null