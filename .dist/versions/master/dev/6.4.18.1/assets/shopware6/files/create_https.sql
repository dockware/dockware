-- Add a HTTPS endpoint to the sales channel
-- We grab the HTTP and insert it as HTTPS
INSERT IGNORE INTO sales_channel_domain (id, sales_channel_id, language_id, url, currency_id, snippet_set_id)
SELECT '0x78CE07322D8640CBB48C73442A4CA844', sales_channel_id, language_id, 'https://localhost', currency_id, snippet_set_id FROM sales_channel_domain WHERE url = "http://localhost";
