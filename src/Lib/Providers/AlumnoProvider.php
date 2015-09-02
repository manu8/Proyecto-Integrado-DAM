<?php

namespace Lib\Providers;


use Doctrine\Common\Collections\ArrayCollection;
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
     * @param null $offset Número de página (paginación)
     * @return array|null Todos los alumnos registrados
     */
    public function getAlumnos($offset = null)
    {
        $students = null;
        $em = $this->app["orm.em"];
        if($em instanceof \Doctrine\ORM\EntityManager){
            if(!is_null($offset)){
                $query = $em->createQuery('SELECT a FROM Entities:Alumno a');
                $query->setMaxResults(5);
                $query->setFirstResult(($offset - 1) * 5);
                $students = $query->getResult();
            } else $students = $em->getRepository('Entities\Alumno')->findAll();
        }
        return $students;
    }

    public function getAlumnosBy(array $criteria, $offset = null)
    {
        $studentsFinal = null;
        $studentsCompany = new ArrayCollection();
        $studentsKnowledge = new ArrayCollection();
        $studentsStudy = new ArrayCollection();
        $studentsNIF = new ArrayCollection();
        $studentsName = new ArrayCollection();
        $studentsSurnames = new ArrayCollection();

        $em = $this->app['orm.em'];
        foreach($criteria as $key => $value){
            switch($key){
                case 'company':
                    $company = $em->getRepository('Entities\Empresa')->findOneBy(array(
                        'Id' => $criteria[$key]
                    ));
                    $studentsCompany = $company->getAlumnos();
                    break;
                case 'knowledge':
                    $knowledge = $em->getRepository('Entities\HabilidadConocimiento')->findOneBy(array(
                        'Id' => $criteria[$key]
                    ));
                    $studentsKnowledge = $knowledge->getAlumnos();
                    break;
                case 'study':
                    $study = $em->getRepository('Entities\EstudioTitulo')->findOneBy(array(
                        'Id' => $criteria[$key]
                    ));
                    $studentsStudy = $study->getAlumnos();
                    break;
                case 'nif': $studentsNIF->add($em->getRepository('Entities\Alumno')->findBy(array(
                    'NIF' => $criteria[$key]
                )));
                    break;
                case 'name': $studentsName->add($em->getRepository('Entities\Alumno')->findBy(array(
                    'Nombre' => $criteria[$key]
                )));
                    break;
                case 'surnames': $studentsSurnames->add($em->getRepository('Entities\Alumno')->findBy(array(
                    'Apellidos' => $criteria[$key]
                )));
            }
        }
        $studentsFinal = new ArrayCollection(
            $studentsCompany->toArray() +
            $studentsKnowledge->toArray() +
            $studentsStudy->toArray() +
            $studentsNIF->toArray() +
            $studentsName->toArray() +
            $studentsSurnames->toArray()
        );

        if(!is_null($offset)) return array_slice($studentsFinal, ($offset - 1) * 5, 5);
        else return $studentsFinal;
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
            $student = $em->getRepository('Entities\Alumno')->findOneBy(array('Id' => $id));
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
        $em->flush();
    }

    /**
     * Añade un estudio a un alumno
     * @param Alumno $student Alumno al que añadir el estudio
     * @param EstudioTitulo $study Estudio a añadir
     */
    public function addEstudio(Alumno $student, EstudioTitulo $study)
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
    public function addConocimiento(Alumno $student, HabilidadConocimiento $knowledge)
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
    public function addEmpresa(Alumno $student, Empresa $company)
    {
        $em = $this->app['orm.em'];
        $student->addCompany($company);
        $em->persist($student);
        $em->flush();
    }
}