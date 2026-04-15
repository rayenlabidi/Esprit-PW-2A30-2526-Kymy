<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Formation.php';
require_once __DIR__ . '/../models/Job.php';

class DashboardController extends BaseController
{
    public function index(): void
    {
        $stats = [
            'users' => 0,
            'formations' => 0,
            'jobs' => 0,
        ];
        $featuredFormations = [];
        $featuredJobs = [];

        if ($this->db) {
            $userModel = new User($this->db);
            $formationModel = new Formation($this->db);
            $jobModel = new Job($this->db);

            $stats['users'] = $userModel->count();
            $stats['formations'] = $formationModel->stats()['total'];
            $stats['jobs'] = $jobModel->stats()['total'];
            $featuredFormations = $formationModel->featured();
            $featuredJobs = $jobModel->featured();
        }

        $this->render('dashboard/index', compact('stats', 'featuredFormations', 'featuredJobs'));
    }
}
