<?php

namespace Tks\TksBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TranslationKeys
 *
 * @ORM\Table(name="translation_keys")
 * @ORM\Entity
 */
class TranslationKey
implements \Tks\TksBundle\Interfaces\ExportsJson
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=250, nullable=false, unique=true)
     */
    public $name;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="text", nullable=false)
     */
    public $value;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastChanged", type="datetime", nullable=false)
     */
    public $lastchanged;

    /**
     * @var \Deployments
     *
     * @ORM\Column(name="deployment_id", unique=true)
     * @ORM\ManyToOne(targetEntity="Deployment", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="deployment_id", referencedColumnName="id", nullable=false)
     * })
     */
    public $deployment;

    /**
     * @var \Languages
     *
     * @ORM\Column(name="language_id", unique=true)
     * @ORM\ManyToOne(targetEntity="Language", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="language_id", referencedColumnName="id", nullable=false)
     * })
     */
    public $language;

    public function save($data) {
        // TODO: some validation
        $this->setName($data->name);
        $this->setValue($data->value);
        $this->setDeployment($data->deployment);
        $this->setLanguage($data->language);
        $this->setLastchanged(new \DateTime('now'));
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return TranslationKey
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return TranslationKey
     */
    public function setValue($value)
    {
        $this->value = $value;
    
        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set lastchanged
     *
     * @param \DateTime $lastchanged
     * @return TranslationKey
     */
    public function setLastchanged($lastchanged)
    {
        $this->lastchanged = $lastchanged;
    
        return $this;
    }

    /**
     * Get lastchanged
     *
     * @return \DateTime 
     */
    public function getLastchanged()
    {
        return $this->lastchanged;
    }

    /**
     * Set deployment
     *
     * @param Deployment $deployment
     * @return TranslationKey
     */
    public function setDeployment(Deployment $deployment = null)
    {
        $this->deployment = $deployment;
    
        return $this;
    }

    /**
     * Get deployment
     *
     * @return \Tks\TksBundle\Entity\Deployment
     */
    public function getDeployment()
    {
        return $this->deployment;
    }

    /**
     * Set language
     *
     * @param Language $language
     * @return TranslationKey
     */
    public function setLanguage(Language $language = null)
    {
        $this->language = $language;
    
        return $this;
    }

    /**
     * Get language
     *
     * @return \Tks\TksBundle\Entity\Languages
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     *
     * @return mixed
     */
    public function exportForJson()
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'value' => $this->value,
            'deployment' => (int)$this->deployment,
            'language' => (int)$this->language,
            'lastChanged' => $this->getLastchanged()->format('Y-m-d H:m:s')
        );
    }

}