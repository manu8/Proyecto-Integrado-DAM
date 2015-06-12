<?php

namespace Lib\Providers;


use Entities\Alumno;
use Entities\Empresa;
use Entities\EstudioTitulo;
use Entities\HabilidadConocimiento;

class AlumnoProvider {

    /**
     * @var \Silex\Application
     */
    private $app;

    public function __construct(\Silex\Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return array|null Todos los alumnos registrados
     */
    public function getAlumnos()
    {
        $students = null;
        $em = $this->app["orm.em"];
        if($em instanceof \Doctrine\ORM\EntityManager){
            $students = $em->getRepository('Alumno')->findAll();
        }
        return $students;
    }

    /**
     * @param integer $id Id del alumno solicitado
     * @return null|object El alumno con el id indicado, si existe
     */
    public function getAlumno($id)
    {
        $student = null;
        $em = $this->app["orm.em"];
        if($em instanceof \Doctrine\ORM\EntityManager){
            $student = $em->getRepository('Alumno')->findOneBy($id);
        }
        return $student;
    }

    /**
     * Crea un nuevo alumno en la base de datos
     * @param Alumno $student Alumno a almacenar en la BD
     */
    public function createAlumno(Alumno $student)
    {
        $em = $this->app['orm.em'];
        $em->persist($student);
        $em->flush();
    }

    /**
     * Modifica un alumno existente en la base de datos
     * @param Alumno $student Alumno a modificar en la BD
     */
    public function updateAlumno(Alumno $student)
    {
        $em = $this->app['orm.em'];
        $em->persist($student);
        $em->flush();
    }

    /**
     * Elimina un alumno existente de la base de datos
     * @param Alumno $student Alumno a eliminar de la BD
     */
    public function removeAlumno(Alumno $student)
    {
        $em = $this->app['orm.em'];
        $em->remove($student);
        $em->flush;
    }

    /**
     * Añade un estudio a un alumno
     * @param Alumno $student Alumno al que añadir el estudio
     * @param EstudioTitulo $study Estudio a añadir
     */
    public function addStudy(Alumno $student, EstudioTitulo $study)
    {
        $em = $this->app['orm.em'];
        $student->addStudy($study);
        $em->persist($student);
        $em->flush();
    }

    /**
     * Añade un conocimiento o habilidad a un alumno
     * @param Alumno $student Alumno al que añadir el conocimiento o habilidad
     * @param HabilidadConocimiento $knowledge Conocimiento o habilidad a añadir
     */
    public function addKnowledge(Alumno $student, HabilidadConocimiento $knowledge)
    {
        $em = $this->app['orm.em'];
        $student->addKnowledge($knowledge);
        $em->persist($student);
        $em->flush();
    }

    /**
     * Añade una empresa a un alumno
     * @param Alumno $student Alumno al que añadir la empresa
     * @param Empresa $company Empresa a añadir
     */
    public function addCompany(Alumno $student, Empresa $company)
    {
        $em = $this->app['orm.em'];
        $student->addCompany($company);
        $em->persist($student);
        $em->flush();
    }
}