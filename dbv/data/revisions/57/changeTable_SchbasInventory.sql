ALTER TABLE `SchbasInventory`
	ADD UNIQUE `ixBookYearOfPurchaseExemplar` (
		`book_id`, `year_of_purchase`, `exemplar`
	);