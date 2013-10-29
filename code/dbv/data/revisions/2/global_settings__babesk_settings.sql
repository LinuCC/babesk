-- Default values for settings of the headmodule BaBeSk
INSERT INTO `global_settings`
	(name, value) VALUES
	-- The Price users with solidarity supplement enabled have to pay
	('soli_price', 1.00),
	-- strtotime-like description to begin displaying the
	-- meal-order-list
	('displayMealsStartdate', 'last Monday'),
	-- strtotime-like description to end displaying the meal-order-list
	('displayMealsEnddate', 'this Friday +1 weeks'),
	-- strtotime-like description the ordering ends. Begins from 0:00 at the
	-- day the respective meal is served
	('orderEnddate', 'now +8 Hours'),
	-- strtotime-like description the cancelling of an order is no more
	-- allowed for the meal. Begins from 0:00 at the day the respective meal
	-- is served
	('ordercancelEnddate', 'now +8 Hours'),
	-- If the solidarity supplement (soli) is used in the program or not
	('solipriceEnabled', 1),
	-- How many Meals a User is allowed to order per day
	('maxCountOfOrdersPerDayPerUser', 1),
	-- A piece of information displayed at the bottom of the meal-order-list
	('menu_text1', ''),
	-- A piece of information displayed at the bottom of the meal-order-list
	('menu_text2', '');
