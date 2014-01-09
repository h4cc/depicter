<?php


namespace Silpion\Depicter\Report;


class Checker
{

    public function findViolations(File $file)
    {
        $messages = array();

        foreach ($file->getFiles() as $path) {
            $entry = $file->getFile($path);
            foreach ($entry['comments'] as $comments) {
                foreach ($comments as $comment) {
                    if (!isset($messages[$comment['id']])) {
                        $messages[$comment['id']] = 0;
                    }
                    $messages[$comment['id']]++;
                }
            }
        }

        // Sort the violations from highest to lowest count.
        arsort($messages, SORT_NUMERIC);

        return $messages;
    }

}
 