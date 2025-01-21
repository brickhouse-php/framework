<?php

namespace Brickhouse\Benchmarks\Proofs;

use PhpBench\Attributes as Bench;

#[Bench\BeforeMethods("setUp")]
class HashingAlgorithmBench
{
    /**
     * @var string
     */
    private $string = '';

    public function setUp(array $params): void
    {
        $byteLength = (int) ceil($params['size'] / 2);

        $this->string = bin2hex(random_bytes($byteLength));
    }

    // #[Bench\Revs(100)]
    #[Bench\ParamProviders(["provideHashAlgorithms", "provideStringLength"])]
    public function benchHashAlgorithms(array $params)
    {
        hash($params['algo'], $this->string);
    }

    public function provideHashAlgorithms()
    {
        yield ['algo' => 'md5'];
        yield ['algo' => 'sha1'];
        yield ['algo' => 'sha256'];
        yield ['algo' => 'sha512'];
        yield ['algo' => 'xxh3'];
        yield ['algo' => 'xxh128'];
    }

    public function provideStringLength()
    {
        yield ['size' => 10];
        yield ['size' => 100];
        yield ['size' => 1000];
    }
}
