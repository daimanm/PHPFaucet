CREATE TABLE `reward` (
  `id` int(111) NOT NULL,
  `ip` text NOT NULL,
  `address` text NOT NULL,
  `useragent` text NOT NULL,
  `datum` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `referer` text NOT NULL,
  `amount` decimal(10,8) NOT NULL,
  `txid` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `reward`
  ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `reward`
  MODIFY `id` int(111) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
