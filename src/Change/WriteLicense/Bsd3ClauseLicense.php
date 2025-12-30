<?php

declare(strict_types=1);

namespace Vjik\Scaffolder\Change\WriteLicense;

use Vjik\Scaffolder\Context;
use Vjik\Scaffolder\Fact\CopyrightHolder;
use Vjik\Scaffolder\Fact\CopyrightYear;
use Vjik\Scaffolder\Fact\CopyrightYearValue;

/**
 * BSD 3-Clause "New" or "Revised" License
 *
 * @see https://spdx.org/licenses/BSD-3-Clause.html
 */
final readonly class Bsd3ClauseLicense implements LicenseInterface
{
    public function __construct(
        private ?string $owner = null,
        private ?CopyrightYearValue $year = null,
    ) {
    }

    public function render(Context $context): string
    {
        $year = ($this->year ?? $context->getFact(CopyrightYear::class))->renderAscii();
        $owner = $this->owner ?? $context->getFact(CopyrightHolder::class);
        return <<<LICENSE
            Copyright (c) $year $owner.

            Redistribution and use in source and binary forms, with or without modification, are permitted provided that the
            following conditions are met:
            
            1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following
               disclaimer.
            
            2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following
               disclaimer in the documentation and/or other materials provided with the distribution.
            
            3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote products
               derived from this software without specific prior written permission.
            
            THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
            INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
            DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
            SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
            SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
            WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
            THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

            LICENSE;
    }
}
