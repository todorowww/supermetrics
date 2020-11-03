<?php

namespace todorowww;

class PostStats
{

    /**
     * Holds total post lengths, per month
     *
     * @var array
     */
    private $postLengths = [];

    /**
     * Holds number of posts, per month
     *
     * @var array
     */
    private $postCount = [];

    /**
     * Holds the longest post length, per month
     *
     * @var array
     */
    private $longestPostLength = [];

    /**
     * Holds the longest post, per month
     *
     * @var array
     */
    private $longestPost = [];

    /**
     * Holds total number of posts, by week
     *
     * @var array
     */
    private $postsByWeek = [];


    /**
     * Holds average number of posting users, per month
     *
     * @var array
     */
    private $users = [];

    /**
     * Gather all the needed data here, and perform all the pre-processing that can be made ahead of time.
     *
     * @param $posts
     * @throws \Exception
     */
    public function processPosts($posts)
    {
        foreach ($posts as $post) {
            $postDate = new \DateTime($post->created_time);
            $month = (int)$postDate->format("m");
            $week = (int)$postDate->format("W");
            $user = $post->from_id;

            /**
             * Next three block were purposefully left here, in order to reduce the amount of memory needed
             * to initialize the variables. When operating on such small amount of data, slowdown is negligible.
             * If need be, initialization block can be put outside the loop, reducing run time, but increasing
             * memory usage.
             */
            if (!isset($this->postLengths[$month])) {
                $this->postLengths[$month] = 0;
            }

            if (!isset($this->postCount[$month])) {
                $this->postCount[$month] = 0;
            }

            if (!isset($this->postsByWeek[$week])) {
                $this->postsByWeek[$week] = 0;
            }

            if (!isset($this->users[$month][$user])) {
                $this->users[$month][$user] = 0;
            }

            $this->postLengths[$month] += mb_strlen($post->message);
            $this->postCount[$month]++;
            $this->postsByWeek[$week]++;
            $this->users[$month][$user]++;

            if (isset($this->longestPostLength[$month])) {
                if ($this->longestPostLength[$month] < mb_strlen($post->message)) {
                    $this->longestPostLength[$month] = mb_strlen($post->message);
                    $this->longestPost[$month] = $post;
                }
            } else {
                $this->longestPostLength[$month] = mb_strlen($post->message);
                $this->longestPost[$month] = $post;
            }
        }
    }

    /**
     * Returns average post length, per month.
     *
     * @param null $month Optional. If specified, return the results only for that month. Otherwise, return results
     *                    for all available months.
     * @return array
     */
    public function getAveragePostLengthPerMonth($month = null): array
    {
        $response = [];

        if ($month) {
            $length = $this->postLengths[$month] ?? 0;
            $posts = $this->postCount[$month] ?? 0;
            $response[$month] = $posts > 0 ? $length / $posts : 0;
        } else {
            for ($i = 1; $i <= 12; $i++) {
                $length = $this->postLengths[$i] ?? 0;
                $posts = $this->postCount[$i] ?? 0;
                $result = round($posts > 0 ? $length / $posts : 0, 1);

                // If 0, leave out the data
                if ($result > 0) {
                    $response[$i] = $result;
                }
            }
        }
        return $response;
    }

    /**
     * Returns the longest post contents, per month.
     *
     * @param null $month Optional. If specified, return the results only for that month. Otherwise, return results
     *                    for all available months.
     * @return array
     */
    public function getLongestPostByMonth($month = null): array
    {
        $response = [];

        if ($month) {
            $response[$month] = $this->longestPost[$month] ?? 0;
        } else {
            for ($i = 1; $i <= 12; $i++) {
                $result = $this->longestPost[$i] ?? null;

                // If empty leave out the data
                if ($result) {
                    $response[$i] = $result;
                }

            }
        }
        return $response;
    }

    /**
     * Returns longest post length, per month.
     *
     * @param null $month Optional. If specified, return the results only for that month. Otherwise, return results
     *                    for all available months.
     * @return array
     */
    public function getLongestPostLengthByMonth($month = null): array
    {
        $response = [];

        if ($month) {
            $response[$month] = $this->longestPostLength[$month] ?? 0;
        } else {
            for ($i = 1; $i <= 12; $i++) {
                $result = $this->longestPostLength[$i] ?? 0;

                // If 0, leave out the data
                if ($result > 0) {
                    $response[$i] = $result;
                }

            }
        }
        return $response;
    }

    /**
     * Returns total number of posts, by week
     *
     * @param null $week Optional. If specified, return the results only for that week. Otherwise, return results
     *                   for all available weeks.
     * @return array
     */
    public function getTotalPostsByWeek($week = null): array
    {
        $response = [];

        if ($week) {
            $response[$week] = $this->postsByWeek[$week] ?? 0;
        } else {
            for ($i = 1; $i <= 52; $i++) {
                $result = $this->postsByWeek[$i] ?? 0;

                // If 0, leave out the data
                if ($result > 0) {
                    $response[$i] = $result;
                }

            }
        }

        return $response;
    }

    /**
     * Returns average number of posts per user, per month.
     *
     * @param null $month Optional. If specified, return the results only for that month. Otherwise, return results
     *                    for all available months.
     * @return array
     */
    public function getAverageNumberOfPostsPerUserPerMonth($month = null): array
    {
        $response = [];
        if ($month) {
            $numActiveUsers = isset($this->users[$month]) ? count($this->users[$month]) : 1;
            $posts = $this->postCount[$month] ?? 0;
            $response[$month] = round($posts / $numActiveUsers, 1);
        } else {
            for ($i = 1; $i <= 12; $i++) {
                $numActiveUsers = isset($this->users[$i]) ? count($this->users[$i]) : 1;
                $posts = $this->postCount[$i] ?? 0;
                $result = round($posts / $numActiveUsers, 1);

                // If 0, leave out the data
                if ($result > 0) {
                    $response[$i] = $result;
                }
            }
        }
        return $response;
    }
}
