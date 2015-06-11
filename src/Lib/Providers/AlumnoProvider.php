<?php

namespace Lib\Providers;


use Entities\Alumno;
use Entities\Empresa;
use Entities\Estudio_Titulo;
use Entities\Habilidad_Conocimiento;

class AlumnoProvider {

    private $app;

    public function __construct(\Silex\Application $app)
    {
        $this->app = $app;
    }

    public function getAlumno($id)
    {
        $student = null;
        $em = $this->app["orm.em"];
        if($em instanceof \Doctrine\ORM\EntityManager){
            $student = $em->getRepository('Alumno')->findOneBy($id);
        }
        return $student;
    }

    public function createAlumno(Alumno $student)
    {
        $em = $this->app['orm.em'];
        $em->persist($student);
        $em->flush();
    }

    public function updateAlumno(Alumno $student)
    {
        $em = $this->app['orm.em'];
        $em->persist($student);
        $em->flush();
    }

    public function removeAlumno(Alumno $student)
    {
        $em = $this->app['orm.em'];
        $em->remove($student);
        $em->flush;
    }

    public function addStudy(Alumno $student, Estudio_Titulo $study)
    {
        $em = $this->app['orm.em'];
        $student->addStudy($study);
        $em->persist($student);
        $em->flush();
    }

    public function addKnowledge(Alumno $student, Habilidad_Conocimiento $knowledge)
    {
        $em = $this->app['orm.em'];
        $student->addKnowledge($knowledge);
        $em->persist($student);
        $em->flush();
    }

    public function addCompany(Alumno $student, Empresa $company)
    {
        $em = $this->app['orm.em'];
        $student->addCompany($company);
        $em->persist($student);
        $em->flush();
    }
}