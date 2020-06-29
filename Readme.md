for youtube download
php /home/ceek/html/ceek/index.php page=manage_youtube type=auto cron_id=without_login id=22
for cron job command
*/1 * * * * php /home/ceek/html/ceek/index.php page=cron_job cron_id=without_login /dev/null 2>&1 &