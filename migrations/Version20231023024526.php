<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231023024526 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'exams DB schema';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE answers (id UUID NOT NULL, challenge_id UUID DEFAULT NULL, question_id UUID NOT NULL, option_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_50D0C60698A21AC6 ON answers (challenge_id)');
        $this->addSql('CREATE INDEX IDX_50D0C6061E27F6BF ON answers (question_id)');
        $this->addSql('CREATE INDEX IDX_50D0C606A7C41D6F ON answers (option_id)');
        $this->addSql('COMMENT ON COLUMN answers.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN answers.challenge_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN answers.question_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN answers.option_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE challenges (id UUID NOT NULL, exam_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, finished_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, examinee_id VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7B5A7E0578D5E91 ON challenges (exam_id)');
        $this->addSql('COMMENT ON COLUMN challenges.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN challenges.exam_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN challenges.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN challenges.finished_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE exams (id UUID NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN exams.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE options (id UUID NOT NULL, question_id UUID NOT NULL, content VARCHAR(255) NOT NULL, is_correct BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D035FA871E27F6BF ON options (question_id)');
        $this->addSql('COMMENT ON COLUMN options.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN options.question_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE questions (id UUID NOT NULL, exam_id UUID NOT NULL, content VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8ADC54D5578D5E91 ON questions (exam_id)');
        $this->addSql('COMMENT ON COLUMN questions.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN questions.exam_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE answers ADD CONSTRAINT FK_50D0C60698A21AC6 FOREIGN KEY (challenge_id) REFERENCES challenges (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE answers ADD CONSTRAINT FK_50D0C6061E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE answers ADD CONSTRAINT FK_50D0C606A7C41D6F FOREIGN KEY (option_id) REFERENCES options (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE challenges ADD CONSTRAINT FK_7B5A7E0578D5E91 FOREIGN KEY (exam_id) REFERENCES exams (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE options ADD CONSTRAINT FK_D035FA871E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D5578D5E91 FOREIGN KEY (exam_id) REFERENCES exams (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE answers ADD CONSTRAINT UNIQUE_CHALLENGE_OPTION_CONSTRAINT UNIQUE (challenge_id, question_id, option_id);');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE answers DROP CONSTRAINT UNIQUE_CHALLENGE_OPTION_CONSTRAINT;');
        $this->addSql('ALTER TABLE answers DROP CONSTRAINT FK_50D0C60698A21AC6');
        $this->addSql('ALTER TABLE answers DROP CONSTRAINT FK_50D0C6061E27F6BF');
        $this->addSql('ALTER TABLE answers DROP CONSTRAINT FK_50D0C606A7C41D6F');
        $this->addSql('ALTER TABLE challenges DROP CONSTRAINT FK_7B5A7E0578D5E91');
        $this->addSql('ALTER TABLE options DROP CONSTRAINT FK_D035FA871E27F6BF');
        $this->addSql('ALTER TABLE questions DROP CONSTRAINT FK_8ADC54D5578D5E91');
        $this->addSql('DROP TABLE answers');
        $this->addSql('DROP TABLE challenges');
        $this->addSql('DROP TABLE exams');
        $this->addSql('DROP TABLE options');
        $this->addSql('DROP TABLE questions');
    }
}
