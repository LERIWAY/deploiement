<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Utilisateur
 *
 * @ORM\Table(name="Utilisateur")
 * @ORM\Entity
 */
class Utilisateur implements \JsonSerializable
{
    /**
     * @var string
     *
     * @ORM\Column(name="login", type="text")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="Utilisateur_login_seq", allocationSize=1, initialValue=1)
     */
    private $login;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="text", nullable=false)
     */
    private $password;

    /**
     * Get login.
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set password.
     *
     * @param string $password
     *
     * @return Utilisateur
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Specify data which should be serialized to JSON
     * Serializes the object to a value that can be serialized natively by json_encode().
     * @return mixed Returns data which can be serialized by json_encode(), which is a value of any type other than a resource .
     */
    public function jsonSerialize() {
        $vars = get_object_vars($this);
        unset($vars['password']);
        return $vars;
    }
}
